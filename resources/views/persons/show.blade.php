@extends('layouts.app')

@section('title', $person->name . ' ' . $person->last_name)
@section('page-title', $person->name . ' ' . $person->last_name)
@section('page-sub', 'Member profile details')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('persons.index') }}" class="text-decoration-none text-muted">Members</a>
    </li>
    <li class="breadcrumb-item active">{{ $person->name }} {{ $person->last_name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Profile header card --}}
        <div class="card mb-3">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width:60px; height:60px;
                                background:linear-gradient(135deg,#3b82f6,#8b5cf6);
                                border-radius:50%; display:flex; align-items:center;
                                justify-content:center; color:#fff; font-size:1.5rem;
                                font-weight:700; flex-shrink:0;">
                        {{ strtoupper(substr($person->name, 0, 1)) }}
                    </div>
                    <div>
                        <h5 class="mb-0 fw-700" style="font-weight:700;">
                            {{ $person->name }} {{ $person->last_name }}
                        </h5>
                        <small class="text-muted">Member #{{ $person->id }}</small>
                    </div>
                    <div class="ms-auto d-flex gap-2">
                        <a href="{{ route('persons.edit', $person) }}"
                           class="btn btn-sm"
                           style="background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; border:none;">
                            <i class="bi bi-pencil-fill me-1"></i>Edit
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="delBtn">
                            <i class="bi bi-trash3-fill me-1"></i>Delete
                        </button>
                    </div>
                </div>

                <div class="row g-3">
                    {{-- Name --}}
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#f8fafc;">
                            <div style="font-size:.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px;">
                                First Name
                            </div>
                            <div style="font-weight:600; color:#111827;">{{ $person->name }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#f8fafc;">
                            <div style="font-size:.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px;">
                                Last Name
                            </div>
                            <div style="font-weight:600; color:#111827;">{{ $person->last_name }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#f8fafc;">
                            <div style="font-size:.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px;">
                                Province
                            </div>
                            <div style="font-weight:500; color:#374151;">{{ $person->province ?: '—' }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#f8fafc;">
                            <div style="font-size:.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px;">
                                Education
                            </div>
                            <div style="font-weight:500; color:#374151;">{{ $person->education ?: '—' }}</div>
                        </div>
                    </div>
                    @if(!auth()->user()->isViewer())
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#f8fafc;">
                            <div style="font-size:.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px;">
                                Email Address
                            </div>
                            @if($person->email)
                                <a href="mailto:{{ $person->email }}" class="text-decoration-none text-primary" style="font-size:.875rem;">
                                    {{ $person->email }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#f8fafc;">
                            <div style="font-size:.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px;">
                                Phone Number
                            </div>
                            @if($person->phone)
                                <a href="tel:{{ $person->phone }}" class="text-decoration-none text-primary" style="font-size:.875rem;">
                                    {{ $person->phone }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                    </div>
                    {{-- WhatsApp --}}
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#f0fdf4;">
                            <div style="font-size:.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px;">
                                WhatsApp
                            </div>
                            @if($person->whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/[^\d]/','',$person->whatsapp) }}"
                                   target="_blank" rel="noopener"
                                   class="text-decoration-none text-success" style="font-size:.875rem;">
                                    <i class="bi bi-whatsapp me-1"></i>{{ $person->whatsapp }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                    </div>
                    @endif
                    {{-- Country --}}
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#f8fafc;">
                            <div style="font-size:.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px;">
                                Country
                            </div>
                            <div style="font-weight:500; color:#374151;">{{ $person->country ?: '—' }}</div>
                        </div>
                    </div>
                    {{-- Gender --}}
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#f8fafc;">
                            <div style="font-size:.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px;">
                                Gender
                            </div>
                            <div style="font-weight:500; color:#374151;">{{ $person->gender ?: '—' }}</div>
                        </div>
                    </div>
                    {{-- Event Name --}}
                    @if(!auth()->user()->isViewer())
                    <div class="col-12">
                        <div class="p-3 rounded-3" style="background:#fff7ed;">
                            <div style="font-size:.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px;">
                                Event Name
                            </div>
                            @if($person->event_name)
                                <span class="badge" style="background:#fed7aa; color:#c2410c; font-size:.82rem; font-weight:500;">
                                    {{ $person->event_name }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                    </div>
                    @endif
                    {{-- Notes --}}
                    @if($person->notes)
                    <div class="col-12">
                        <div class="p-3 rounded-3" style="background:#f8fafc; border:1px solid #e5e7eb;">
                            <div style="font-size:.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.6px; margin-bottom:6px;">
                                Notes
                            </div>
                            <div style="font-size:.875rem; color:#374151; white-space:pre-wrap;">{{ $person->notes }}</div>
                        </div>
                    </div>
                    @endif
                </div>{{-- end .row.g-3 --}}

                <hr style="border-color:#f3f4f6; margin: 1.25rem 0 1rem;">
                @if(!auth()->user()->isViewer())
                <div class="d-flex justify-content-between" style="font-size:.75rem; color:#9ca3af;">
                    <span><i class="bi bi-calendar-plus me-1"></i>Added {{ $person->created_at->format('M d, Y  H:i') }}</span>
                    <span><i class="bi bi-pencil me-1"></i>Updated {{ $person->updated_at->format('M d, Y  H:i') }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('persons.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Back to List
            </a>
        </div>
    </div>
</div>

{{-- Delete modal --}}
<div class="modal fade" id="delModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:14px; overflow:hidden; border:none;">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:38px; height:38px; background:#fef2f2; border-radius:10px;
                                display:flex; align-items:center; justify-content:center; color:#ef4444;">
                        <i class="bi bi-trash3-fill"></i>
                    </div>
                    <h5 class="modal-title mb-0" style="font-size:.95rem; font-weight:700;">Delete Member</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2" style="font-size:.875rem;">
                Are you sure you want to delete
                <strong>{{ $person->name }} {{ $person->last_name }}</strong>?
                <br><small class="text-danger">This action cannot be undone.</small>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('persons.destroy', $person) }}">
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
document.getElementById('delBtn').addEventListener('click', function () {
    new bootstrap.Modal(document.getElementById('delModal')).show();
});
</script>
@endpush
