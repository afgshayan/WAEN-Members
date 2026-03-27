<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'filename',
        'original_name',
        'path',
        'disk',
        'mime_type',
        'size',
        'type',
        'uploaded_by',
        'alt_text',
        'description',
    ];

    protected $casts = [
        'size'       => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ── Relationships ──

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ── Accessors ──

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getIsImageAttribute(): bool
    {
        return $this->type === 'image';
    }

    public function getExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
    }

    // ── Scopes ──

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;
        $like = '%' . $term . '%';
        return $query->where(function ($q) use ($like) {
            $q->where('original_name', 'LIKE', $like)
              ->orWhere('alt_text', 'LIKE', $like)
              ->orWhere('description', 'LIKE', $like);
        });
    }

    public function scopeOfType($query, ?string $type)
    {
        if (!$type) return $query;
        return $query->where('type', $type);
    }

    // ── Helpers ──

    public static function determineType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) return 'image';
        if (in_array($mimeType, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])) return 'document';
        return 'file';
    }

    public static function storeUpload(\Illuminate\Http\UploadedFile $file, ?int $userId = null): self
    {
        $mime     = $file->getMimeType();
        $type     = self::determineType($mime);
        $folder   = match ($type) {
            'image'    => 'media/images',
            'document' => 'media/documents',
            default    => 'media/files',
        };

        $storedPath = $file->store($folder, 'public');

        return self::create([
            'filename'      => basename($storedPath),
            'original_name' => $file->getClientOriginalName(),
            'path'          => $storedPath,
            'disk'          => 'public',
            'mime_type'     => $mime,
            'size'          => $file->getSize(),
            'type'          => $type,
            'uploaded_by'   => $userId,
        ]);
    }

    /**
     * Track a file that was already stored (e.g. headshot/CV from PersonController).
     */
    public static function trackExisting(\Illuminate\Http\UploadedFile $file, string $storedPath, ?int $userId = null): self
    {
        return self::create([
            'filename'      => basename($storedPath),
            'original_name' => $file->getClientOriginalName(),
            'path'          => $storedPath,
            'disk'          => 'public',
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
            'type'          => self::determineType($file->getMimeType()),
            'uploaded_by'   => $userId,
        ]);
    }

    public function deleteFile(): void
    {
        Storage::disk($this->disk)->delete($this->path);
    }
}
