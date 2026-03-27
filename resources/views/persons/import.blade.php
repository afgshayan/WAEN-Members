@extends('layouts.app')

@section('title', 'Import CSV')
@section('page-title', 'Import Members')
@section('page-sub', 'Upload a CSV file to bulk-import member records')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('persons.index') }}" class="text-decoration-none text-muted">Members</a>
    </li>
    <li class="breadcrumb-item active">Import CSV</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Upload card --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <div style="width:32px; height:32px; background:linear-gradient(135deg,#6b7280,#374151);
                            border-radius:8px; display:flex; align-items:center; justify-content:center;
                            color:#fff; font-size:.85rem; flex-shrink:0;">
                    <i class="bi bi-upload"></i>
                </div>
                <span>Upload CSV File</span>
            </div>
            <div class="card-body p-4">

                <div class="alert d-flex gap-3 mb-4"
                     style="background:#eff6ff; color:#1e40af; border:none; border-radius:10px;">
                    <i class="bi bi-info-circle-fill fs-5 mt-1 flex-shrink-0"></i>
                    <div>
                        <strong>Requirements</strong>
                        <ul class="mb-0 mt-1 ps-3" style="font-size:.84rem;">
                            <li>File format: <code>.csv</code> or <code>.txt</code></li>
                            <li>First row must be the header. Only required column:
                                <code>name</code>
                            </li>
                            <li>Optional columns:
                                <code>province</code>, <code>country</code>,
                                <code>email</code>, <code>phone</code>, <code>whatsapp</code>,
                                <code>education</code>, <code>gender</code>,
                                <code>event_name</code>, <code>notes</code>
                            </li>
                            <li><code>gender</code> accepted values: <code>Male</code>, <code>Female</code>, <code>Other</code></li>
                            <li>Encoding: <strong>UTF-8</strong> (with or without BOM)</li>
                            <li>Maximum file size: <strong>50 MB</strong></li>
                            <li>Duplicate email addresses are automatically skipped.</li>
                        </ul>
                    </div>
                </div>

                <form method="POST" action="{{ route('persons.import') }}"
                      enctype="multipart/form-data" novalidate>
                    @csrf

                    <div class="mb-4">
                        <label for="csv_file" class="form-label fw-semibold" style="font-size:.875rem;">
                            Select CSV File <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="csv_file" id="csv_file"
                               class="form-control @error('csv_file') is-invalid @enderror"
                               accept=".csv,.txt" required>
                        @error('csv_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text" style="font-size:.78rem;">Accepted: .csv, .txt — Max 50 MB</div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('persons.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancel
                        </a>
                        <a href="{{ route('persons.sample') }}" class="btn btn-outline-primary">
                            <i class="bi bi-file-earmark-arrow-down me-1"></i>Download Sample CSV
                        </a>
                        <button type="submit" class="btn btn-primary" id="uploadBtn">
                            <i class="bi bi-upload me-1"></i>Start Import
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sample card --}}
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-text text-muted"></i>
                <span>Sample CSV Template</span>
            </div>
            <div class="card-body p-4">
                <pre class="p-3 rounded-3 mb-2" style="background:#f8fafc; font-size:.8rem; color:#374151; border:1px solid #e5e7eb; overflow-x:auto;">name,last_name,province,country,email,phone,whatsapp,education,gender,event_name,notes
Ahmad,Rahimi,Kabul,Afghanistan,ahmad@example.com,+93-700-123456,+93-700-123456,Bachelor's Degree,Male,Annual Conference,
Fatima,Noori,Herat,Afghanistan,fatima@example.com,+93-799-654321,,,Female,,</pre>
                <small class="text-muted" style="font-size:.78rem;">
                    <i class="bi bi-lightbulb me-1 text-warning"></i>
                    All columns except <em>name</em> are optional.
                    Rows without a <em>name</em> are skipped.
                </small>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelector('form').addEventListener('submit', function () {
    var btn  = document.getElementById('uploadBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Processing…';
});
</script>
@endpush
