{{-- WordPress-style Media Picker Modal --}}
<div class="modal fade" id="mediaPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:12px; overflow:hidden; min-height:70vh;">
            {{-- Header --}}
            <div class="modal-header border-0 px-4 py-3" style="background:linear-gradient(135deg,#667eea,#764ba2);">
                <h5 class="modal-title text-white fw-bold" id="mediaPickerTitle">
                    <i class="bi bi-images me-2"></i>Select Media
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Tabs --}}
            <ul class="nav nav-tabs px-4 pt-3" id="mediaPickerTabs" role="tablist" style="border-bottom:2px solid #e5e7eb;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold" id="mp-library-tab"
                            data-bs-toggle="tab" data-bs-target="#mp-library-pane"
                            type="button" role="tab" style="font-size:.875rem;">
                        <i class="bi bi-grid-3x3-gap me-1"></i>Media Library
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="mp-upload-tab"
                            data-bs-toggle="tab" data-bs-target="#mp-upload-pane"
                            type="button" role="tab" style="font-size:.875rem;">
                        <i class="bi bi-cloud-arrow-up me-1"></i>Upload New
                    </button>
                </li>
            </ul>

            {{-- Body --}}
            <div class="modal-body p-0" style="min-height:400px;">
                <div class="tab-content h-100">

                    {{-- ═══ Library Tab ═══ --}}
                    <div class="tab-pane fade show active h-100" id="mp-library-pane" role="tabpanel">
                        <div class="d-flex h-100">
                            {{-- Left: Grid --}}
                            <div class="flex-grow-1 p-3" style="overflow-y:auto; max-height:55vh;">
                                {{-- Search bar --}}
                                <div class="d-flex gap-2 mb-3">
                                    <div class="input-group input-group-sm" style="max-width:300px;">
                                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                        <input type="text" class="form-control" id="mpSearch" placeholder="Search files…">
                                    </div>
                                    <select class="form-select form-select-sm" id="mpTypeFilter" style="max-width:150px;">
                                        <option value="">All Types</option>
                                        <option value="image">Images</option>
                                        <option value="document">Documents</option>
                                        <option value="file">Other</option>
                                    </select>
                                </div>

                                {{-- Grid --}}
                                <div id="mpGrid" class="row g-2">
                                    {{-- Populated via JS --}}
                                </div>

                                {{-- Loading / Empty --}}
                                <div id="mpLoading" class="text-center py-5 d-none">
                                    <div class="spinner-border text-primary" role="status" style="width:2rem; height:2rem;"></div>
                                    <p class="mt-2 text-muted mb-0" style="font-size:.85rem;">Loading media…</p>
                                </div>
                                <div id="mpEmpty" class="text-center py-5 d-none">
                                    <i class="bi bi-inbox" style="font-size:3rem; color:#cbd5e1;"></i>
                                    <p class="mt-2 text-muted mb-0" style="font-size:.85rem;">No media files found.</p>
                                    <button type="button" class="btn btn-sm btn-primary mt-2" onclick="document.getElementById('mp-upload-tab').click()">
                                        <i class="bi bi-cloud-arrow-up me-1"></i>Upload Now
                                    </button>
                                </div>

                                {{-- Load More --}}
                                <div id="mpLoadMore" class="text-center py-3 d-none">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="mpLoadMoreBtn">
                                        <i class="bi bi-arrow-down-circle me-1"></i>Load More
                                    </button>
                                </div>
                            </div>

                            {{-- Right: Detail sidebar --}}
                            <div id="mpDetailSidebar" class="border-start p-3 d-none"
                                 style="width:280px; min-width:280px; overflow-y:auto; max-height:55vh; background:#fafbfc;">
                                <div class="text-center mb-3">
                                    <div id="mpDetailPreview" style="width:100%; max-height:180px; border-radius:8px; overflow:hidden; background:#f1f5f9; display:flex; align-items:center; justify-content:center;">
                                    </div>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size:.82rem;" id="mpDetailName"></h6>
                                <div class="text-muted mb-2" style="font-size:.72rem;" id="mpDetailMeta"></div>
                                <hr class="my-2">
                                <div style="font-size:.78rem;">
                                    <div class="mb-1"><strong>Type:</strong> <span id="mpDetailType"></span></div>
                                    <div class="mb-1"><strong>Size:</strong> <span id="mpDetailSize"></span></div>
                                    <div class="mb-1"><strong>Date:</strong> <span id="mpDetailDate"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ═══ Upload Tab ═══ --}}
                    <div class="tab-pane fade h-100" id="mp-upload-pane" role="tabpanel">
                        <div class="p-4">
                            {{-- Drop zone --}}
                            <div id="mpDropZone" class="border-2 border-dashed rounded-3 text-center py-5 px-4"
                                 style="border-color:#cbd5e1; background:#fafbfc; cursor:pointer; transition:all .2s;">
                                <i class="bi bi-cloud-arrow-up" style="font-size:3rem; color:#94a3b8;"></i>
                                <h6 class="fw-bold mt-2 mb-1" style="color:#475569;">Drop files here or click to browse</h6>
                                <p class="text-muted mb-0" style="font-size:.82rem;">
                                    Images: JPG, PNG, WebP (max 5 MB) &nbsp;|&nbsp; Documents: PDF, DOC, DOCX (max 10 MB)
                                </p>
                                <input type="file" id="mpFileInput" class="d-none" multiple
                                       accept="image/jpeg,image/png,image/webp,.pdf,.doc,.docx">
                            </div>

                            {{-- Upload progress --}}
                            <div id="mpUploadList" class="mt-3"></div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <div class="modal-footer border-top px-4 py-3" style="background:#f8fafc;">
                <span id="mpSelectedInfo" class="text-muted me-auto" style="font-size:.82rem;"></span>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="mpSelectBtn" disabled>
                    <i class="bi bi-check2-circle me-1"></i>Select
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';

    var modal, currentCallback, currentFilter, selectedMedia, currentPage, lastPage, searchTimeout;
    var mediaCache = [];

    // ── Initialize ──
    function init() {
        modal = new bootstrap.Modal(document.getElementById('mediaPickerModal'));

        document.getElementById('mpSelectBtn').addEventListener('click', confirmSelection);
        document.getElementById('mpSearch').addEventListener('input', debounceSearch);
        document.getElementById('mpTypeFilter').addEventListener('change', function() { loadMedia(1); });
        document.getElementById('mpLoadMoreBtn').addEventListener('click', function() { loadMedia(currentPage + 1, true); });

        // Drop zone
        var dropZone = document.getElementById('mpDropZone');
        var fileInput = document.getElementById('mpFileInput');

        dropZone.addEventListener('click', function() { fileInput.click(); });
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault(); e.stopPropagation();
            this.style.borderColor = '#3b82f6'; this.style.background = '#eff6ff';
        });
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault(); e.stopPropagation();
            this.style.borderColor = '#cbd5e1'; this.style.background = '#fafbfc';
        });
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault(); e.stopPropagation();
            this.style.borderColor = '#cbd5e1'; this.style.background = '#fafbfc';
            if (e.dataTransfer.files.length) uploadFiles(e.dataTransfer.files);
        });
        fileInput.addEventListener('change', function() {
            if (this.files.length) uploadFiles(this.files);
            this.value = '';
        });
    }

    // ── Public API ──
    window.MediaPicker = {
        open: function(options) {
            options = options || {};
            currentCallback = options.onSelect || function() {};
            currentFilter = options.type || '';
            selectedMedia = null;
            currentPage = 1;
            lastPage = 1;
            mediaCache = [];

            // Set title
            var title = options.title || 'Select Media';
            document.getElementById('mediaPickerTitle').innerHTML = '<i class="bi bi-images me-2"></i>' + title;

            // Set type filter
            var typeSelect = document.getElementById('mpTypeFilter');
            typeSelect.value = currentFilter;

            // Reset UI
            document.getElementById('mpSearch').value = '';
            document.getElementById('mpGrid').innerHTML = '';
            document.getElementById('mpDetailSidebar').classList.add('d-none');
            document.getElementById('mpSelectBtn').disabled = true;
            document.getElementById('mpSelectedInfo').textContent = '';
            document.getElementById('mpUploadList').innerHTML = '';

            // Switch to library tab
            var libTab = document.getElementById('mp-library-tab');
            bootstrap.Tab.getOrCreateInstance(libTab).show();

            // Set file input accept based on type
            var fileInput = document.getElementById('mpFileInput');
            if (currentFilter === 'image') {
                fileInput.accept = 'image/jpeg,image/png,image/webp';
            } else if (currentFilter === 'document') {
                fileInput.accept = '.pdf,.doc,.docx';
            } else {
                fileInput.accept = 'image/jpeg,image/png,image/webp,.pdf,.doc,.docx';
            }

            modal.show();
            loadMedia(1);
        }
    };

    // ── Load media from server ──
    function loadMedia(page, append) {
        var search = document.getElementById('mpSearch').value.trim();
        var type = document.getElementById('mpTypeFilter').value || currentFilter;

        var params = new URLSearchParams({
            page: page,
            per_page: 48,
            sort: 'created_at',
            direction: 'desc'
        });
        if (search) params.append('search', search);
        if (type) params.append('type', type);

        var url = '{{ route("media.index") }}?' + params.toString();

        toggle('mpLoading', true);
        toggle('mpEmpty', false);
        toggle('mpLoadMore', false);
        if (!append) {
            document.getElementById('mpGrid').innerHTML = '';
            mediaCache = [];
        }

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            toggle('mpLoading', false);
            currentPage = data.current_page;
            lastPage = data.last_page;

            if (data.data.length === 0 && !append) {
                toggle('mpEmpty', true);
                return;
            }

            data.data.forEach(function(item) { mediaCache.push(item); });
            renderGrid(append ? data.data : mediaCache);

            if (currentPage < lastPage) {
                toggle('mpLoadMore', true);
            }
        })
        .catch(function() {
            toggle('mpLoading', false);
        });
    }

    // ── Render media grid ──
    function renderGrid(items) {
        var grid = document.getElementById('mpGrid');
        if (!arguments[1]) grid.innerHTML = ''; // only clear for initial load

        items.forEach(function(item) {
            // Skip if already rendered (for append)
            if (grid.querySelector('[data-media-id="' + item.id + '"]')) return;

            var col = document.createElement('div');
            col.className = 'col-4 col-sm-3 col-md-2';

            var card = document.createElement('div');
            card.className = 'mp-item border rounded-2 text-center p-1 position-relative';
            card.style.cssText = 'cursor:pointer; transition:all .15s; aspect-ratio:1; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#f8fafc;';
            card.dataset.mediaId = item.id;

            if (item.is_image) {
                var img = document.createElement('img');
                img.src = item.url;
                img.alt = item.original_name;
                img.style.cssText = 'max-width:100%; max-height:100%; object-fit:cover; border-radius:4px;';
                img.loading = 'lazy';
                card.appendChild(img);
            } else {
                var iconWrap = document.createElement('div');
                iconWrap.innerHTML = '<i class="bi ' + getFileIcon(item.extension) + '" style="font-size:2rem; color:#64748b;"></i>' +
                    '<div class="text-truncate mt-1" style="font-size:.65rem; color:#64748b; max-width:90%;">' +
                    escapeHtml(item.original_name) + '</div>';
                iconWrap.style.cssText = 'text-align:center; padding:8px;';
                card.appendChild(iconWrap);
            }

            // Check indicator
            var check = document.createElement('div');
            check.className = 'mp-check d-none';
            check.style.cssText = 'position:absolute; top:4px; right:4px; width:22px; height:22px; background:#3b82f6; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:.7rem;';
            check.innerHTML = '<i class="bi bi-check-lg"></i>';
            card.appendChild(check);

            card.addEventListener('click', function() { selectItem(item, card); });
            col.appendChild(card);
            grid.appendChild(col);
        });
    }

    // ── Select item ──
    function selectItem(item, cardEl) {
        // Deselect previous
        var prev = document.querySelector('.mp-item.mp-selected');
        if (prev) {
            prev.classList.remove('mp-selected');
            prev.style.borderColor = '';
            prev.style.boxShadow = '';
            var prevCheck = prev.querySelector('.mp-check');
            if (prevCheck) prevCheck.classList.add('d-none');
        }

        selectedMedia = item;
        cardEl.classList.add('mp-selected');
        cardEl.style.borderColor = '#3b82f6';
        cardEl.style.boxShadow = '0 0 0 2px rgba(59,130,246,.4)';
        var check = cardEl.querySelector('.mp-check');
        if (check) check.classList.remove('d-none');

        // Enable select button
        document.getElementById('mpSelectBtn').disabled = false;
        document.getElementById('mpSelectedInfo').textContent = item.original_name + ' (' + item.human_size + ')';

        // Show detail sidebar
        showDetail(item);
    }

    // ── Show detail sidebar ──
    function showDetail(item) {
        var sidebar = document.getElementById('mpDetailSidebar');
        sidebar.classList.remove('d-none');

        var preview = document.getElementById('mpDetailPreview');
        if (item.is_image) {
            preview.innerHTML = '<img src="' + item.url + '" style="max-width:100%; max-height:180px; object-fit:contain;">';
        } else {
            preview.innerHTML = '<i class="bi ' + getFileIcon(item.extension) + '" style="font-size:3rem; color:#64748b;"></i>';
        }

        document.getElementById('mpDetailName').textContent = item.original_name;
        document.getElementById('mpDetailMeta').textContent = item.filename;
        document.getElementById('mpDetailType').textContent = item.mime_type;
        document.getElementById('mpDetailSize').textContent = item.human_size;
        document.getElementById('mpDetailDate').textContent = item.created_at;
    }

    // ── Confirm selection ──
    function confirmSelection() {
        if (selectedMedia && currentCallback) {
            currentCallback(selectedMedia);
        }
        modal.hide();
    }

    // ── Upload files ──
    function uploadFiles(fileList) {
        var uploadList = document.getElementById('mpUploadList');
        var formData = new FormData();

        for (var i = 0; i < fileList.length; i++) {
            formData.append('files[]', fileList[i]);

            // Show progress item
            var item = document.createElement('div');
            item.className = 'd-flex align-items-center gap-2 p-2 border rounded mb-2';
            item.id = 'mp-upload-' + i;
            item.innerHTML =
                '<div style="width:36px; height:36px; background:#f1f5f9; border-radius:8px; display:flex; align-items:center; justify-content:center;">' +
                    '<i class="bi bi-file-earmark-arrow-up text-primary"></i>' +
                '</div>' +
                '<div class="flex-grow-1" style="min-width:0;">' +
                    '<div class="fw-semibold text-truncate" style="font-size:.8rem;">' + escapeHtml(fileList[i].name) + '</div>' +
                    '<div class="progress mt-1" style="height:4px;">' +
                        '<div class="progress-bar bg-primary" role="progressbar" style="width:0%"></div>' +
                    '</div>' +
                '</div>';
            uploadList.appendChild(item);
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("media.upload") }}');
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                var pct = Math.round((e.loaded / e.total) * 100);
                uploadList.querySelectorAll('.progress-bar').forEach(function(bar) {
                    bar.style.width = pct + '%';
                });
            }
        });

        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                var data = JSON.parse(xhr.responseText);
                uploadList.innerHTML =
                    '<div class="alert alert-success py-2 px-3" style="font-size:.85rem;">' +
                    '<i class="bi bi-check-circle-fill me-1"></i>' + data.count + ' file(s) uploaded successfully.</div>';

                // Switch to library tab and reload
                setTimeout(function() {
                    document.getElementById('mp-library-tab').click();
                    loadMedia(1);
                }, 500);
            } else {
                var errMsg = 'Upload failed.';
                try {
                    var errData = JSON.parse(xhr.responseText);
                    if (errData.message) errMsg = errData.message;
                    if (errData.errors) {
                        var errs = [];
                        Object.keys(errData.errors).forEach(function(k) {
                            errs = errs.concat(errData.errors[k]);
                        });
                        errMsg = errs.join(' ');
                    }
                } catch(e) {}
                uploadList.innerHTML =
                    '<div class="alert alert-danger py-2 px-3" style="font-size:.85rem;">' +
                    '<i class="bi bi-exclamation-triangle-fill me-1"></i>' + escapeHtml(errMsg) + '</div>';
            }
        };

        xhr.onerror = function() {
            uploadList.innerHTML =
                '<div class="alert alert-danger py-2 px-3" style="font-size:.85rem;">' +
                '<i class="bi bi-exclamation-triangle-fill me-1"></i>Network error. Please try again.</div>';
        };

        xhr.send(formData);
    }

    // ── Helpers ──
    function debounceSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() { loadMedia(1); }, 350);
    }

    function toggle(id, show) {
        var el = document.getElementById(id);
        if (el) el.classList.toggle('d-none', !show);
    }

    function getFileIcon(ext) {
        var map = {
            pdf: 'bi-file-earmark-pdf-fill',
            doc: 'bi-file-earmark-word-fill',
            docx: 'bi-file-earmark-word-fill',
            xls: 'bi-file-earmark-excel-fill',
            xlsx: 'bi-file-earmark-excel-fill',
            jpg: 'bi-file-earmark-image-fill',
            jpeg: 'bi-file-earmark-image-fill',
            png: 'bi-file-earmark-image-fill',
            webp: 'bi-file-earmark-image-fill',
        };
        return map[ext] || 'bi-file-earmark-fill';
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    // ── Boot ──
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
