@extends('layouts.app')

@section('title', 'Media Library')
@section('page-title', 'Media Library')
@section('page-sub', 'Upload, browse and manage all files')
@section('breadcrumb')
    <li class="breadcrumb-item active">Media Library</li>
@endsection

@section('content')
<style>
    .media-drop-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 14px;
        padding: 2.5rem 1rem;
        text-align: center;
        transition: all .25s;
        background: #f8fafc;
        cursor: pointer;
        position: relative;
    }
    .media-drop-zone.dragging {
        border-color: #3b82f6;
        background: #eff6ff;
    }
    .media-drop-zone .drop-icon {
        font-size: 2.5rem;
        color: #94a3b8;
        margin-bottom: .5rem;
    }
    .media-drop-zone.dragging .drop-icon { color: #3b82f6; }
    .media-drop-zone p { color: #64748b; font-size: .9rem; margin-bottom: .25rem; }
    .media-drop-zone small { color: #94a3b8; font-size: .78rem; }

    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 12px;
    }
    .media-item {
        position: relative;
        border-radius: 10px;
        border: 2px solid transparent;
        overflow: hidden;
        cursor: pointer;
        transition: all .2s;
        background: #f8fafc;
    }
    .media-item:hover { border-color: #3b82f6; box-shadow: 0 2px 12px rgba(59,130,246,.15); }
    .media-item.selected { border-color: #3b82f6; background: #eff6ff; }
    .media-item .media-thumb {
        width: 100%;
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #f1f5f9;
    }
    .media-item .media-thumb img {
        width: 100%; height: 100%;
        object-fit: cover;
    }
    .media-item .media-thumb .file-icon {
        font-size: 2.5rem;
        color: #94a3b8;
    }
    .media-item .media-info {
        padding: 6px 8px;
        font-size: .72rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .media-item .media-info .name { font-weight: 600; color: #334155; }
    .media-item .select-check {
        position: absolute;
        top: 6px; left: 6px;
        z-index: 2;
        width: 22px; height: 22px;
        border-radius: 50%;
        background: rgba(255,255,255,.85);
        border: 2px solid #cbd5e1;
        display: flex; align-items: center; justify-content: center;
        opacity: 0;
        transition: opacity .2s;
    }
    .media-item:hover .select-check,
    .media-item.selected .select-check { opacity: 1; }
    .media-item.selected .select-check { background: #3b82f6; border-color: #3b82f6; color: #fff; }

    /* Detail panel */
    .detail-panel {
        position: fixed;
        top: 0; right: -420px;
        width: 400px;
        height: 100vh;
        background: #fff;
        box-shadow: -4px 0 24px rgba(0,0,0,.1);
        z-index: 1050;
        transition: right .3s ease;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
    }
    .detail-panel.open { right: 0; }
    .detail-panel .panel-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky; top: 0;
        background: #fff;
        z-index: 2;
    }
    .detail-panel .panel-body { padding: 1.25rem; flex: 1; }
    .detail-panel .panel-preview {
        width: 100%;
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 1rem;
    }
    .detail-panel .panel-preview img {
        max-width: 100%; max-height: 100%;
        object-fit: contain;
    }
    .detail-panel .panel-preview .file-icon-lg {
        font-size: 4rem;
        color: #94a3b8;
    }
    .detail-label {
        font-size: .68rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 2px;
    }
    .detail-value {
        font-size: .85rem;
        color: #1e293b;
        word-break: break-all;
        margin-bottom: .75rem;
    }
    .detail-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,.2);
        z-index: 1049;
        display: none;
    }
    .detail-overlay.open { display: block; }

    /* Upload progress */
    .upload-progress-bar {
        height: 4px;
        background: #e2e8f0;
        border-radius: 2px;
        overflow: hidden;
        margin-top: 1rem;
        display: none;
    }
    .upload-progress-bar .bar {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        width: 0;
        transition: width .3s;
        border-radius: 2px;
    }

    /* View toggle */
    .view-toggle .btn { padding: 4px 10px; font-size: .8rem; border-radius: 6px; }
    .view-toggle .btn.active { background: #3b82f6; color: #fff; border-color: #3b82f6; }

    /* List view */
    .media-list-table .thumb-cell { width: 50px; }
    .media-list-table .thumb-cell img {
        width: 40px; height: 40px;
        object-fit: cover; border-radius: 6px;
    }
    .media-list-table .thumb-cell .file-icon-sm {
        width: 40px; height: 40px;
        display: flex; align-items: center; justify-content: center;
        background: #f1f5f9; border-radius: 6px;
        font-size: 1.2rem; color: #94a3b8;
    }
</style>

{{-- ── Stat row ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl">
        <div class="stat-card c-blue">
            <div class="stat-label">Total Files <i class="bi bi-folder-fill"></i></div>
            <div class="stat-val">{{ number_format($stats['total']) }}</div>
            <div class="stat-sub">All uploaded files</div>
        </div>
    </div>
    <div class="col-6 col-xl">
        <div class="stat-card c-green">
            <div class="stat-label">Images <i class="bi bi-image-fill"></i></div>
            <div class="stat-val">{{ number_format($stats['images']) }}</div>
            <div class="stat-sub">Photos & graphics</div>
        </div>
    </div>
    <div class="col-6 col-xl">
        <div class="stat-card c-orange">
            <div class="stat-label">Documents <i class="bi bi-file-earmark-text-fill"></i></div>
            <div class="stat-val">{{ number_format($stats['documents']) }}</div>
            <div class="stat-sub">PDF, DOC, XLS</div>
        </div>
    </div>
    <div class="col-6 col-xl">
        <div class="stat-card c-purple">
            <div class="stat-label">Storage Used <i class="bi bi-hdd-fill"></i></div>
            <div class="stat-val">{{ $stats['totalSize'] >= 1048576 ? round($stats['totalSize']/1048576, 1).' MB' : round($stats['totalSize']/1024, 1).' KB' }}</div>
            <div class="stat-sub">Total disk usage</div>
        </div>
    </div>
</div>

{{-- ── Upload zone ── --}}
@if(!auth()->user()->isViewer())
<div class="card mb-4" id="uploadCard">
    <div class="card-body p-3">
        <form method="POST" action="{{ route('media.upload') }}" enctype="multipart/form-data" id="uploadForm">
            @csrf
            <div class="media-drop-zone" id="dropZone">
                <div class="drop-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                <p><strong>Drop files here</strong> or <span class="text-primary" style="cursor:pointer;">click to browse</span></p>
                <small>Max 20 MB per file · Images, PDFs, documents & more</small>
                <input type="file" name="files[]" id="fileInput" multiple
                       style="position:absolute; inset:0; opacity:0; cursor:pointer;">
            </div>
            <div class="upload-progress-bar" id="progressWrap">
                <div class="bar" id="progressBar"></div>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ── Filter & view bar ── --}}
<div class="filter-card mb-4">
    <form method="GET" action="{{ route('media.index') }}" id="filterForm">
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="direction" value="{{ $direction }}">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-lg-4">
                <label class="form-label small fw-600 mb-1" style="font-weight:600; font-size:.78rem; color:#374151;">
                    <i class="bi bi-search me-1 text-muted"></i>Search
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="File name, description …"
                           value="{{ $search }}" maxlength="200">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-600 mb-1" style="font-weight:600; font-size:.78rem; color:#374151;">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="image" {{ $type === 'image' ? 'selected' : '' }}>Images</option>
                    <option value="document" {{ $type === 'document' ? 'selected' : '' }}>Documents</option>
                    <option value="file" {{ $type === 'file' ? 'selected' : '' }}>Other Files</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-600 mb-1" style="font-weight:600; font-size:.78rem; color:#374151;">Per Page</label>
                <select name="per_page" class="form-select">
                    @foreach([24, 48, 96] as $n)
                        <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-funnel me-1"></i>Filter</button>
                <a href="{{ route('media.index') }}" class="btn btn-outline-secondary" title="Clear"><i class="bi bi-x-circle"></i></a>
            </div>
            <div class="col-6 col-md-2 d-flex justify-content-end align-items-end gap-1 view-toggle">
                <button type="button" class="btn btn-outline-secondary active" id="gridViewBtn" title="Grid view">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary" id="listViewBtn" title="List view">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ── Action bar ── --}}
<div class="action-bar mb-2">
    <div class="d-flex align-items-center gap-2">
        <span class="results-txt">
            Showing <strong>{{ $media->firstItem() ?? 0 }}–{{ $media->lastItem() ?? 0 }}</strong>
            of <strong>{{ number_format($media->total()) }}</strong> files
        </span>
        @if(auth()->user()->isAdmin())
        <button type="button" class="btn btn-danger btn-sm d-none" id="bulkDeleteBtn">
            <i class="bi bi-trash3-fill me-1"></i>Delete Selected (<span id="selectedCount">0</span>)
        </button>
        @endif
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm" id="selectAllBtn">
            <i class="bi bi-check2-square me-1"></i>Select All
        </button>
    </div>
</div>

{{-- ── Grid view ── --}}
<div id="gridView">
    @if($media->count())
    <div class="media-grid">
        @foreach($media as $item)
        <div class="media-item" data-id="{{ $item->id }}">
            <div class="select-check">
                <i class="bi bi-check-lg" style="font-size:.8rem;"></i>
            </div>
            <div class="media-thumb">
                @if($item->is_image)
                    <img src="{{ $item->url }}" alt="{{ $item->alt_text ?? $item->original_name }}" loading="lazy">
                @else
                    <div class="file-icon">
                        @if($item->type === 'document')
                            <i class="bi bi-file-earmark-pdf-fill" style="color:#ef4444;"></i>
                        @else
                            <i class="bi bi-file-earmark-fill"></i>
                        @endif
                    </div>
                @endif
            </div>
            <div class="media-info">
                <div class="name" title="{{ $item->original_name }}">{{ $item->original_name }}</div>
                <div>{{ $item->human_size }} · {{ strtoupper($item->extension) }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-5 text-muted">
        <i class="bi bi-images fs-1 d-block mb-2 opacity-30"></i>
        No media files found.
    </div>
    @endif
</div>

{{-- ── List view (hidden by default) ── --}}
<div id="listView" style="display:none;">
    @if($media->count())
    <div class="card">
        <div class="tbl-wrap">
            <table class="table table-hover media-list-table mb-0">
                <thead>
                    <tr>
                        @if(auth()->user()->isAdmin())
                        <th style="width:44px; padding:.8rem .5rem .8rem 1rem;">
                            <input type="checkbox" id="listSelectAll" class="form-check-input">
                        </th>
                        @endif
                        <th class="thumb-cell"></th>
                        <th>File Name</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Uploaded By</th>
                        <th>Date</th>
                        <th style="width:100px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($media as $item)
                    <tr class="media-list-row" data-id="{{ $item->id }}">
                        @if(auth()->user()->isAdmin())
                        <td style="padding:.72rem .5rem .72rem 1rem;">
                            <input type="checkbox" class="form-check-input list-cb" value="{{ $item->id }}">
                        </td>
                        @endif
                        <td class="thumb-cell">
                            @if($item->is_image)
                                <img src="{{ $item->url }}" alt="" loading="lazy">
                            @else
                                <div class="file-icon-sm">
                                    @if($item->type === 'document')
                                        <i class="bi bi-file-earmark-pdf-fill" style="color:#ef4444;"></i>
                                    @else
                                        <i class="bi bi-file-earmark-fill"></i>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="fw-semibold" style="font-size:.85rem;">{{ Str::limit($item->original_name, 40) }}</span>
                        </td>
                        <td><span class="badge bg-light text-secondary border" style="font-size:.7rem;">{{ strtoupper($item->extension) }}</span></td>
                        <td style="font-size:.82rem;">{{ $item->human_size }}</td>
                        <td style="font-size:.82rem;">{{ $item->uploader?->name ?? '—' }}</td>
                        <td style="font-size:.78rem; color:#64748b;">{{ $item->created_at->format('M d, Y') }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-act btn-outline-info detail-btn" data-id="{{ $item->id }}" title="Details">
                                <i class="bi bi-eye"></i>
                            </button>
                            <a href="{{ route('media.download', $item) }}" class="btn btn-act btn-outline-success" title="Download">
                                <i class="bi bi-download"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

{{-- Pagination --}}
@if($media->hasPages())
<div class="mt-4">{{ $media->links() }}</div>
@endif

{{-- ── Detail side panel ── --}}
<div class="detail-overlay" id="detailOverlay"></div>
<div class="detail-panel" id="detailPanel">
    <div class="panel-header">
        <h6 class="mb-0 fw-bold" style="font-size:.95rem;">
            <i class="bi bi-info-circle me-1 text-primary"></i>File Details
        </h6>
        <button type="button" class="btn-close" id="closePanel"></button>
    </div>
    <div class="panel-body">
        <div class="panel-preview" id="panelPreview"></div>

        <div class="detail-label">File Name</div>
        <div class="detail-value" id="panelName">—</div>

        <div class="detail-label">File URL</div>
        <div class="detail-value">
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" id="panelUrl" readonly style="font-size:.78rem;">
                <button class="btn btn-outline-secondary" type="button" id="copyUrlBtn" title="Copy URL">
                    <i class="bi bi-clipboard"></i>
                </button>
            </div>
        </div>

        <div class="row g-2">
            <div class="col-6">
                <div class="detail-label">Type</div>
                <div class="detail-value" id="panelType">—</div>
            </div>
            <div class="col-6">
                <div class="detail-label">Size</div>
                <div class="detail-value" id="panelSize">—</div>
            </div>
            <div class="col-6">
                <div class="detail-label">Uploaded By</div>
                <div class="detail-value" id="panelUploader">—</div>
            </div>
            <div class="col-6">
                <div class="detail-label">Date</div>
                <div class="detail-value" id="panelDate">—</div>
            </div>
        </div>

        <hr style="border-color:#f1f5f9;">

        <form id="detailForm" method="POST">
            @csrf @method('PUT')
            <div class="mb-2">
                <label class="detail-label">Alt Text</label>
                <input type="text" name="alt_text" id="panelAlt" class="form-control form-control-sm" maxlength="255">
            </div>
            <div class="mb-3">
                <label class="detail-label">Description</label>
                <textarea name="description" id="panelDesc" class="form-control form-control-sm" rows="3" maxlength="2000"></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-sm w-100 mb-2">
                <i class="bi bi-check-lg me-1"></i>Save Details
            </button>
        </form>

        <div class="d-flex gap-2">
            <a href="#" id="panelDownload" class="btn btn-outline-success btn-sm flex-fill">
                <i class="bi bi-download me-1"></i>Download
            </a>
            @if(auth()->user()->isAdmin())
            <button type="button" class="btn btn-outline-danger btn-sm flex-fill" id="panelDeleteBtn">
                <i class="bi bi-trash3 me-1"></i>Delete
            </button>
            @endif
        </div>
    </div>
</div>

{{-- Delete confirm modal --}}
<div class="modal fade" id="delModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:14px; overflow:hidden; border:none;">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:38px; height:38px; background:#fef2f2; border-radius:10px;
                                display:flex; align-items:center; justify-content:center; color:#ef4444;">
                        <i class="bi bi-trash3-fill"></i>
                    </div>
                    <h5 class="modal-title mb-0" style="font-size:.95rem; font-weight:700;">Delete File</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2" style="font-size:.875rem;">
                Are you sure you want to delete <strong id="delFileName"></strong>?
                <br><small class="text-danger">This action cannot be undone.</small>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="delForm" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    var selectedIds = new Set();
    var currentMediaId = null;
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    // ── Drag & drop upload ──
    var dropZone  = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar  = document.getElementById('progressBar');

    if (dropZone) {
        ['dragenter','dragover'].forEach(function(e) {
            dropZone.addEventListener(e, function(ev) { ev.preventDefault(); dropZone.classList.add('dragging'); });
        });
        ['dragleave','drop'].forEach(function(e) {
            dropZone.addEventListener(e, function(ev) { ev.preventDefault(); dropZone.classList.remove('dragging'); });
        });

        dropZone.addEventListener('drop', function(e) {
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                uploadFiles(e.dataTransfer.files);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length) uploadFiles(this.files);
        });
    }

    function uploadFiles(files) {
        var fd = new FormData();
        fd.append('_token', csrfToken);
        for (var i = 0; i < files.length; i++) fd.append('files[]', files[i]);

        progressWrap.style.display = 'block';
        progressBar.style.width = '0%';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("media.upload") }}');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) progressBar.style.width = Math.round((e.loaded/e.total)*100) + '%';
        });

        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                progressBar.style.width = '100%';
                setTimeout(function() { window.location.reload(); }, 500);
            } else {
                progressWrap.style.display = 'none';
                alert('Upload failed. Please try again.');
            }
        };
        xhr.onerror = function() {
            progressWrap.style.display = 'none';
            alert('Upload failed. Please check your connection.');
        };
        xhr.send(fd);
    }

    // ── Grid item click → open detail panel ──
    document.querySelectorAll('.media-item').forEach(function(el) {
        el.addEventListener('click', function(e) {
            if (e.shiftKey || e.ctrlKey) {
                toggleSelect(el);
            } else {
                openDetail(el.dataset.id);
            }
        });
    });

    // List view detail buttons
    document.querySelectorAll('.detail-btn').forEach(function(btn) {
        btn.addEventListener('click', function() { openDetail(this.dataset.id); });
    });

    // ── Select / deselect ──
    function toggleSelect(el) {
        var id = parseInt(el.dataset.id);
        if (selectedIds.has(id)) {
            selectedIds.delete(id);
            el.classList.remove('selected');
        } else {
            selectedIds.add(id);
            el.classList.add('selected');
        }
        refreshBulk();
    }

    function refreshBulk() {
        var btn = document.getElementById('bulkDeleteBtn');
        var cnt = document.getElementById('selectedCount');
        if (btn) {
            cnt.textContent = selectedIds.size;
            btn.classList.toggle('d-none', selectedIds.size === 0);
        }
    }

    var selectAllBtn = document.getElementById('selectAllBtn');
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            var items = document.querySelectorAll('.media-item');
            var allSelected = selectedIds.size === items.length;
            items.forEach(function(el) {
                var id = parseInt(el.dataset.id);
                if (allSelected) {
                    selectedIds.delete(id);
                    el.classList.remove('selected');
                } else {
                    selectedIds.add(id);
                    el.classList.add('selected');
                }
            });
            refreshBulk();
        });
    }

    // List select all
    var listSelectAll = document.getElementById('listSelectAll');
    if (listSelectAll) {
        listSelectAll.addEventListener('change', function() {
            document.querySelectorAll('.list-cb').forEach(function(c) {
                c.checked = listSelectAll.checked;
                var id = parseInt(c.value);
                if (listSelectAll.checked) selectedIds.add(id); else selectedIds.delete(id);
            });
            refreshBulk();
        });
        document.querySelectorAll('.list-cb').forEach(function(c) {
            c.addEventListener('change', function() {
                var id = parseInt(this.value);
                if (this.checked) selectedIds.add(id); else selectedIds.delete(id);
                refreshBulk();
            });
        });
    }

    // Bulk delete
    var bulkBtn = document.getElementById('bulkDeleteBtn');
    if (bulkBtn) {
        bulkBtn.addEventListener('click', function() {
            if (!confirm('Delete ' + selectedIds.size + ' file(s)? This cannot be undone.')) return;
            fetch('{{ route("media.bulk-destroy") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: Array.from(selectedIds) })
            }).then(function(r) { return r.json(); })
              .then(function() { window.location.reload(); })
              .catch(function() { alert('Delete failed.'); });
        });
    }

    // ── Detail panel ──
    var panel   = document.getElementById('detailPanel');
    var overlay = document.getElementById('detailOverlay');

    function openDetail(id) {
        currentMediaId = id;
        panel.classList.add('open');
        overlay.classList.add('open');

        fetch('{{ url("media") }}/' + id, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            var preview = document.getElementById('panelPreview');
            if (d.is_image) {
                preview.innerHTML = '<img src="' + d.url + '" alt="">';
            } else {
                var icon = d.type === 'document' ? 'bi-file-earmark-pdf-fill' : 'bi-file-earmark-fill';
                preview.innerHTML = '<i class="bi ' + icon + ' file-icon-lg"></i>';
            }

            document.getElementById('panelName').textContent = d.original_name;
            document.getElementById('panelUrl').value = d.url;
            document.getElementById('panelType').textContent = d.mime_type;
            document.getElementById('panelSize').textContent = d.human_size;
            document.getElementById('panelUploader').textContent = d.uploaded_by;
            document.getElementById('panelDate').textContent = d.created_at;
            document.getElementById('panelAlt').value = d.alt_text || '';
            document.getElementById('panelDesc').value = d.description || '';
            document.getElementById('panelDownload').href = '{{ url("media") }}/' + id + '/download';

            document.getElementById('detailForm').action = '{{ url("media") }}/' + id;
            document.getElementById('delForm').action = '{{ url("media") }}/' + id;
            document.getElementById('delFileName').textContent = d.original_name;
        });
    }

    function closeDetail() {
        panel.classList.remove('open');
        overlay.classList.remove('open');
        currentMediaId = null;
    }

    document.getElementById('closePanel').addEventListener('click', closeDetail);
    overlay.addEventListener('click', closeDetail);

    // Copy URL
    document.getElementById('copyUrlBtn').addEventListener('click', function() {
        var inp = document.getElementById('panelUrl');
        inp.select();
        navigator.clipboard.writeText(inp.value);
        this.innerHTML = '<i class="bi bi-check-lg"></i>';
        var btn = this;
        setTimeout(function() { btn.innerHTML = '<i class="bi bi-clipboard"></i>'; }, 1500);
    });

    // Panel delete
    var panelDelBtn = document.getElementById('panelDeleteBtn');
    if (panelDelBtn) {
        panelDelBtn.addEventListener('click', function() {
            new bootstrap.Modal(document.getElementById('delModal')).show();
        });
    }

    // Detail form AJAX save
    document.getElementById('detailForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var form = this;
        fetch(form.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: new FormData(form)
        }).then(function(r) { return r.json(); })
          .then(function() {
            var btn = form.querySelector('button[type=submit]');
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Saved!';
            btn.classList.replace('btn-primary', 'btn-success');
            setTimeout(function() {
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Save Details';
                btn.classList.replace('btn-success', 'btn-primary');
            }, 1500);
        });
    });

    // ── View toggle ──
    var gridBtn = document.getElementById('gridViewBtn');
    var listBtn = document.getElementById('listViewBtn');
    var gridDiv = document.getElementById('gridView');
    var listDiv = document.getElementById('listView');

    gridBtn.addEventListener('click', function() {
        gridDiv.style.display = ''; listDiv.style.display = 'none';
        gridBtn.classList.add('active'); listBtn.classList.remove('active');
    });
    listBtn.addEventListener('click', function() {
        gridDiv.style.display = 'none'; listDiv.style.display = '';
        listBtn.classList.add('active'); gridBtn.classList.remove('active');
    });

    // ── List row click → detail ──
    document.querySelectorAll('.media-list-row').forEach(function(row) {
        row.addEventListener('dblclick', function() { openDetail(this.dataset.id); });
    });

})();
</script>
@endpush
