@extends('layouts.app')

@section('title', 'System Update')
@section('page-title', 'System Update')
@section('page-sub', 'Check for and apply updates')
@section('breadcrumb')
    <li class="breadcrumb-item active">Update</li>
@endsection

@section('content')

@if(session('info'))
<div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-info-circle-fill"></i>
    <span>{{ session('info') }}</span>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ── Current version card ─────────────────────────────────────────────── --}}
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-box-seam-fill" style="background:#f0f9ff; color:#0284c7;"></i>
                    Installed Version
                </div>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="badge fs-5 px-3 py-2" style="background:#f0f9ff; color:#0369a1; font-weight:700; border-radius:10px;">
                        v{{ $localVersion }}
                    </span>
                    <span class="text-muted small">Currently installed</span>
                </div>
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <i class="bi bi-github"></i>
                    <a href="https://github.com/{{ config('update.repo') }}" target="_blank" class="text-muted">
                        github.com/{{ config('update.repo') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100" id="remoteCard">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-cloud-arrow-down-fill" style="background:#f0fdf4; color:#16a34a;"></i>
                    Latest Version
                </div>
                <button class="btn btn-sm btn-outline-secondary" id="recheckBtn" onclick="doCheck()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Check Now
                </button>
            </div>
            <div class="card-body p-4" id="remoteBody">
                @if($updateInfo)
                    @if($updateInfo['has_update'])
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="badge fs-5 px-3 py-2" style="background:#f0fdf4; color:#15803d; font-weight:700; border-radius:10px;">
                                v{{ $updateInfo['version'] }}
                            </span>
                            <span class="badge bg-warning text-dark">Update Available</span>
                        </div>
                        @if(!empty($updateInfo['changelog']))
                        <div class="small text-muted mb-3">
                            <strong>Changelog:</strong> {{ $updateInfo['changelog'] }}
                        </div>
                        @endif
                        <div class="small text-muted">Last checked: {{ $updateInfo['checked_at'] ?? '—' }}</div>
                    @else
                        <div class="d-flex align-items-center gap-2 text-success mb-2">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                            <strong>You are up to date!</strong>
                        </div>
                        <div class="small text-muted">Latest version: v{{ $updateInfo['version'] }}</div>
                        <div class="small text-muted">Last checked: {{ $updateInfo['checked_at'] ?? '—' }}</div>
                    @endif
                @else
                    <div class="text-muted small d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history"></i>
                        Not checked yet. Click "Check Now" to check for updates.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── Update action card ───────────────────────────────────────────────── --}}
<div class="card mb-4" id="updateActionCard" style="{{ ($updateInfo && $updateInfo['has_update']) ? '' : 'display:none;' }}">
    <div class="card-header">
        <div class="card-header-title">
            <i class="bi bi-lightning-charge-fill" style="background:#fffbeb; color:#d97706;"></i>
            Apply Update
        </div>
    </div>
    <div class="card-body p-4">
        <div class="alert alert-warning d-flex align-items-start gap-2 mb-4">
            <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
            <div>
                <strong>Before updating:</strong>
                <ul class="mb-0 mt-1 ps-3 small">
                    <li>The update will download and overwrite application files from GitHub.</li>
                    <li>Your <code>.env</code>, database, uploaded files and <code>storage/</code> folder are <strong>never touched</strong>.</li>
                    <li>Database migrations will run automatically after the update.</li>
                    <li>All caches will be cleared automatically.</li>
                </ul>
            </div>
        </div>

        <form method="POST" action="{{ route('update.run') }}" id="updateForm">
            @csrf
            <button type="submit" class="btn btn-success px-4" id="updateBtn"
                    onclick="return confirm('Start update now? The page may take a moment to load.')">
                <i class="bi bi-cloud-arrow-down me-2"></i>
                Update to v<span id="newVersionBadge">{{ $updateInfo['version'] ?? '' }}</span> Now
            </button>
        </form>
    </div>
</div>

{{-- ── What is protected card ───────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header">
        <div class="card-header-title">
            <i class="bi bi-shield-check-fill" style="background:#fdf2f8; color:#9333ea;"></i>
            Protected Files (never overwritten)
        </div>
    </div>
    <div class="card-body p-4">
        <div class="row g-2">
            @foreach(['.env' => 'Environment & credentials', 'storage/' => 'Uploaded files & logs', 'bootstrap/cache/' => 'Bootstrap cache', '.git/' => 'Git repository data', 'public/install/' => 'Web installer'] as $path => $desc)
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-2 p-2 rounded" style="background:#f8fafc; border:1px solid #e8edf3;">
                    <i class="bi bi-shield-fill-check text-success"></i>
                    <code class="small text-muted">{{ $path }}</code>
                    <span class="text-muted small ms-1">— {{ $desc }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function doCheck() {
    const btn  = document.getElementById('recheckBtn');
    const body = document.getElementById('remoteBody');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Checking…';

    fetch('{{ route('update.check') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Check Now';

        if (data.error) {
            body.innerHTML = '<div class="text-danger small"><i class="bi bi-exclamation-circle me-1"></i>' + data.error + '</div>';
            return;
        }

        if (data.has_update) {
            body.innerHTML =
                '<div class="d-flex align-items-center gap-3 mb-3">' +
                '<span class="badge fs-5 px-3 py-2" style="background:#f0fdf4;color:#15803d;font-weight:700;border-radius:10px;">v' + data.version + '</span>' +
                '<span class="badge bg-warning text-dark">Update Available</span>' +
                '</div>' +
                (data.changelog ? '<div class="small text-muted mb-2"><strong>Changelog:</strong> ' + data.changelog + '</div>' : '') +
                '<div class="small text-muted">Checked: ' + (data.checked_at || '') + '</div>';

            document.getElementById('updateActionCard').style.display = '';
            document.getElementById('newVersionBadge').textContent = data.version;
        } else {
            body.innerHTML =
                '<div class="d-flex align-items-center gap-2 text-success mb-2"><i class="bi bi-check-circle-fill fs-5"></i><strong>You are up to date!</strong></div>' +
                '<div class="small text-muted">Latest: v' + data.version + '</div>' +
                '<div class="small text-muted">Checked: ' + (data.checked_at || '') + '</div>';

            document.getElementById('updateActionCard').style.display = 'none';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Check Now';
        body.innerHTML = '<div class="text-danger small"><i class="bi bi-exclamation-circle me-1"></i>Network error. Please try again.</div>';
    });
}
</script>
@endpush
