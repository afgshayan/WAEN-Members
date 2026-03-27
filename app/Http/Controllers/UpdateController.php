<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use ZipArchive;

class UpdateController extends Controller
{
    private const CACHE_KEY = 'update_check_result';

    /** Paths that must NEVER be overwritten during an update */
    private const PROTECTED = [
        '.env',
        'storage/',
        'bootstrap/cache/',
        '.git/',
        'node_modules/',
        'public/install/',
    ];

    public function __construct()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can manage updates.');
        }
    }

    // -------------------------------------------------------------------------
    // Show update page
    // -------------------------------------------------------------------------

    public function index()
    {
        $localVersion = $this->getLocalVersion();
        $updateInfo   = Cache::get(self::CACHE_KEY);

        return view('update.index', compact('localVersion', 'updateInfo'));
    }

    // -------------------------------------------------------------------------
    // Force a fresh version check (called via AJAX or page button)
    // -------------------------------------------------------------------------

    public function check()
    {
        Cache::forget(self::CACHE_KEY);
        $result = $this->fetchRemoteVersion();

        if ($result) {
            $days = (int) config('update.cache_days', 7);
            Cache::put(self::CACHE_KEY, $result, now()->addDays($days));
        }

        return response()->json($result ?? ['error' => 'Could not reach update server.']);
    }

    // -------------------------------------------------------------------------
    // Perform the update
    // -------------------------------------------------------------------------

    public function doUpdate()
    {
        // Re-check remote before doing anything
        $remote = $this->fetchRemoteVersion();

        if (!$remote || empty($remote['has_update'])) {
            return back()->with('info', 'No update available or update server unreachable.');
        }

        $repo   = config('update.repo');
        $branch = config('update.branch', 'main');
        $zipUrl = "https://github.com/{$repo}/archive/refs/heads/{$branch}.zip";

        $tmpZip = storage_path('app/update_tmp.zip');
        $tmpDir = storage_path('app/update_extracted');

        // ── 1. Stream-download ZIP to disk ────────────────────────────────────
        try {
            if (!is_dir(dirname($tmpZip))) {
                @mkdir(dirname($tmpZip), 0755, true);
            }

            $response = Http::timeout(300)
                ->withOptions([
                    'verify' => false,
                    'sink'   => $tmpZip,  // stream directly to disk — no RAM spike
                ])
                ->get($zipUrl);

            if (!$response->successful() || !file_exists($tmpZip) || filesize($tmpZip) < 1000) {
                @unlink($tmpZip);
                return back()->withErrors(['update' => 'Download failed (status ' . $response->status() . '). Please try again.']);
            }
        } catch (\Throwable $e) {
            @unlink($tmpZip);
            return back()->withErrors(['update' => 'Download error: ' . $e->getMessage()]);
        }

        // ── 2. Extract ZIP ────────────────────────────────────────────────────
        $zip = new ZipArchive();
        $opened = $zip->open($tmpZip);
        if ($opened !== true) {
            @unlink($tmpZip);
            return back()->withErrors(['update' => 'Could not open ZIP (code ' . $opened . '). File may be corrupt.']);
        }

        $this->rmdirRecursive($tmpDir);
        @mkdir($tmpDir, 0755, true);
        $zip->extractTo($tmpDir);
        $zip->close();
        @unlink($tmpZip);

        // ── 3. Find top-level folder inside the ZIP (e.g. "repo-main/") ───────
        $topDirs = glob($tmpDir . '/*', GLOB_ONLYDIR);
        if (empty($topDirs)) {
            $this->rmdirRecursive($tmpDir);
            return back()->withErrors(['update' => 'Unexpected ZIP structure — no top-level folder found.']);
        }

        $extractedRoot = rtrim($topDirs[0], '/\\') . DIRECTORY_SEPARATOR;
        $projectRoot   = rtrim(base_path(), '/\\') . DIRECTORY_SEPARATOR;

        // ── 4. Copy files using file_put_contents (more reliable than copy()) ─
        $copied  = 0;
        $skipped = 0;
        $errors  = [];

        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($extractedRoot, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iter as $item) {
            // Relative path with forward slashes
            $rel = str_replace('\\', '/', substr($item->getPathname(), strlen($extractedRoot)));

            if ($this->isProtected($rel)) {
                $skipped++;
                continue;
            }

            $target = $projectRoot . $rel;

            if ($item->isDir()) {
                if (!is_dir($target)) {
                    @mkdir($target, 0755, true);
                }
                continue;
            }

            // Ensure parent directory exists
            $targetDir = dirname($target);
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            // Read source content and write to destination
            $content = file_get_contents($item->getPathname());
            if ($content === false) {
                $errors[] = 'Read failed: ' . $rel;
                continue;
            }

            $result = file_put_contents($target, $content, LOCK_EX);
            if ($result === false) {
                $errors[] = 'Write failed: ' . $rel;
            } else {
                $copied++;
            }
        }

        // ── 5. Cleanup temp directory ─────────────────────────────────────────
        $this->rmdirRecursive($tmpDir);

        if ($copied === 0 && !empty($errors)) {
            return back()->withErrors(['update' => 'Update failed — no files could be written. Errors: ' . implode('; ', array_slice($errors, 0, 3))]);
        }

        // ── 6. Update local version.json (already copied above, but ensure it) 
        if (isset($remote['version'])) {
            file_put_contents(base_path('version.json'), json_encode([
                'version'   => $remote['version'],
                'changelog' => $remote['changelog'] ?? '',
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        // ── 7. Run DB migrations ──────────────────────────────────────────────
        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable) {}

        // ── 8. Clear all caches ───────────────────────────────────────────────
        foreach (['config:clear', 'cache:clear', 'view:clear', 'route:clear'] as $cmd) {
            try { Artisan::call($cmd); } catch (\Throwable) {}
        }

        // Clear compiled view files manually
        foreach (glob(storage_path('framework/views/*.php')) as $f) {
            @unlink($f);
        }

        Cache::forget(self::CACHE_KEY);

        $newVersion = $remote['version'] ?? 'latest';
        $writeErrors = !empty($errors) ? (' (' . count($errors) . ' files could not be overwritten)') : '';

        return redirect()->route('persons.index')
            ->with('success', "Successfully updated to v{$newVersion}! {$copied} files updated.{$writeErrors}");
    }

    // =========================================================================
    // Private helpers
    // =========================================================================

    private function getLocalVersion(): string
    {
        try {
            $data = json_decode(file_get_contents(base_path('version.json')), true);
            return $data['version'] ?? '0.0.0';
        } catch (\Throwable) {
            return '0.0.0';
        }
    }

    private function fetchRemoteVersion(): ?array
    {
        $repo   = config('update.repo');
        $branch = config('update.branch', 'main');
        $url    = "https://raw.githubusercontent.com/{$repo}/{$branch}/version.json";

        try {
            $response = Http::timeout(10)->withOptions(['verify' => false])->get($url);

            if (!$response->successful()) return null;

            $data = $response->json();
            if (!isset($data['version'])) return null;

            $local              = $this->getLocalVersion();
            $data['has_update'] = version_compare($data['version'], $local, '>');
            $data['local']      = $local;
            $data['checked_at'] = now()->toDateTimeString();

            return $data;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Recursively copy $src directory into $dst, skipping protected paths.
     */
    private function copyDirectory(string $src, string $dst): void
    {
        $src = rtrim($src, '/\\') . DIRECTORY_SEPARATOR;
        $dst = rtrim($dst, '/\\') . DIRECTORY_SEPARATOR;

        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($src, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iter as $item) {
            $rel = str_replace('\\', '/', substr($item->getPathname(), strlen($src)));

            if ($this->isProtected($rel)) continue;

            $target = $dst . $rel;

            if ($item->isDir()) {
                @mkdir($target, 0755, true);
            } else {
                @mkdir(dirname($target), 0755, true);
                @copy($item->getPathname(), $target);
            }
        }
    }

    private function isProtected(string $rel): bool
    {
        foreach (self::PROTECTED as $p) {
            $p = ltrim($p, '/');
            if ($rel === $p || str_starts_with($rel, $p)) {
                return true;
            }
        }
        return false;
    }

    private function rmdirRecursive(string $dir): void
    {
        if (!is_dir($dir)) return;

        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iter as $f) {
            $f->isDir() ? @rmdir($f->getPathname()) : @unlink($f->getPathname());
        }

        @rmdir($dir);
    }
}
