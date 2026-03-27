<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    // ---------------------------------------------------------------------------
    // Index — Media Library (grid / list view)
    // ---------------------------------------------------------------------------

    public function index(Request $request)
    {
        if (auth()->user()->isViewer()) abort(403);

        $request->validate([
            'search'    => 'nullable|string|max:200',
            'type'      => 'nullable|string|in:image,document,file',
            'sort'      => 'nullable|string|in:created_at,original_name,size',
            'direction' => 'nullable|string|in:asc,desc',
            'per_page'  => 'nullable|integer|in:24,48,96',
        ]);

        $search    = $request->input('search');
        $type      = $request->input('type');
        $sort      = in_array($request->input('sort'), ['created_at', 'original_name', 'size'])
                        ? $request->input('sort') : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';
        $perPage   = (int) $request->input('per_page', 48);

        $media = Media::query()
            ->search($search)
            ->ofType($type)
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        // JSON response for media picker modal
        if ($request->expectsJson()) {
            return response()->json([
                'data' => $media->map(fn($m) => [
                    'id'            => $m->id,
                    'original_name' => $m->original_name,
                    'filename'      => $m->filename,
                    'url'           => $m->url,
                    'mime_type'     => $m->mime_type,
                    'size'          => $m->size,
                    'human_size'    => $m->human_size,
                    'type'          => $m->type,
                    'is_image'      => $m->is_image,
                    'extension'     => $m->extension,
                    'created_at'    => $m->created_at->format('M d, Y'),
                ]),
                'current_page' => $media->currentPage(),
                'last_page'    => $media->lastPage(),
                'total'        => $media->total(),
            ]);
        }

        $stats = [
            'total'     => Media::count(),
            'images'    => Media::where('type', 'image')->count(),
            'documents' => Media::where('type', 'document')->count(),
            'files'     => Media::where('type', 'file')->count(),
            'totalSize' => Media::sum('size'),
        ];

        return view('media.index', compact(
            'media', 'stats', 'search', 'type', 'sort', 'direction', 'perPage'
        ));
    }

    // ---------------------------------------------------------------------------
    // Upload — handles drag-drop & file input (AJAX + normal)
    // ---------------------------------------------------------------------------

    public function upload(Request $request)
    {
        if (auth()->user()->isViewer()) abort(403);

        $request->validate([
            'files'   => 'required|array|min:1|max:20',
            'files.*' => 'required|file|max:20480', // 20 MB each
        ]);

        $uploaded = [];

        foreach ($request->file('files') as $file) {
            $media = Media::storeUpload($file, auth()->id());
            $uploaded[] = $media;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count'   => count($uploaded),
                'files'   => collect($uploaded)->map(fn($m) => [
                    'id'            => $m->id,
                    'original_name' => $m->original_name,
                    'url'           => $m->url,
                    'human_size'    => $m->human_size,
                    'type'          => $m->type,
                    'is_image'      => $m->is_image,
                ]),
            ]);
        }

        return redirect()->route('media.index')
            ->with('success', count($uploaded) . ' file(s) uploaded successfully.');
    }

    // ---------------------------------------------------------------------------
    // Show — single media details (JSON for modal)
    // ---------------------------------------------------------------------------

    public function show(Media $medium)
    {
        if (auth()->user()->isViewer()) abort(403);

        if (request()->expectsJson()) {
            return response()->json([
                'id'            => $medium->id,
                'original_name' => $medium->original_name,
                'filename'      => $medium->filename,
                'url'           => $medium->url,
                'mime_type'     => $medium->mime_type,
                'size'          => $medium->size,
                'human_size'    => $medium->human_size,
                'type'          => $medium->type,
                'is_image'      => $medium->is_image,
                'extension'     => $medium->extension,
                'alt_text'      => $medium->alt_text,
                'description'   => $medium->description,
                'uploaded_by'   => $medium->uploader?->name ?? 'Unknown',
                'created_at'    => $medium->created_at->format('M d, Y H:i'),
                'updated_at'    => $medium->updated_at->format('M d, Y H:i'),
            ]);
        }

        return view('media.show', compact('medium'));
    }

    // ---------------------------------------------------------------------------
    // Update — edit alt text / description
    // ---------------------------------------------------------------------------

    public function update(Request $request, Media $medium)
    {
        if (auth()->user()->isViewer()) abort(403);

        $validated = $request->validate([
            'alt_text'    => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
        ]);

        $medium->update($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('media.index')
            ->with('success', 'Media details updated.');
    }

    // ---------------------------------------------------------------------------
    // Destroy — delete single file
    // ---------------------------------------------------------------------------

    public function destroy(Media $medium)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can delete media.');
        }

        $medium->deleteFile();
        $medium->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('media.index')
            ->with('success', 'File deleted successfully.');
    }

    // ---------------------------------------------------------------------------
    // Bulk delete
    // ---------------------------------------------------------------------------

    public function bulkDestroy(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can delete media.');
        }

        $request->validate([
            'ids'   => 'required|array|min:1|max:100',
            'ids.*' => 'required|integer|exists:media,id',
        ]);

        $items = Media::whereIn('id', $request->ids)->get();
        foreach ($items as $item) {
            $item->deleteFile();
            $item->delete();
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'deleted' => count($items)]);
        }

        return redirect()->route('media.index')
            ->with('success', count($items) . ' file(s) deleted.');
    }

    // ---------------------------------------------------------------------------
    // Download
    // ---------------------------------------------------------------------------

    public function download(Media $medium)
    {
        if (auth()->user()->isViewer()) abort(403);

        if (!Storage::disk($medium->disk)->exists($medium->path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk($medium->disk)->download($medium->path, $medium->original_name);
    }
}
