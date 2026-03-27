<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Role helpers ────────────────────────────────────────────

    /** Full administrator — can do everything. */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Editor or admin — can create and edit members, but not delete
     * or manage users / settings.
     */
    public function canEdit(): bool
    {
        return in_array($this->role, ['admin', 'editor'], true);
    }

    /**
     * Viewer — read-only access to member list and profiles.
     */
    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    /** Human-readable role label. */
    public function roleLabel(): string
    {
        return match ($this->role) {
            'admin'  => 'Administrator',
            'editor' => 'Editor',
            'viewer' => 'Viewer',
            default  => ucfirst($this->role ?? 'Unknown'),
        };
    }
}
