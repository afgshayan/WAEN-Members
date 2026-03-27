@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'User Management')
@section('page-sub', 'Manage portal access and roles')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="mb-0 fw-bold">All Users</h5>
        <div class="text-muted" style="font-size:.8rem;">{{ $users->count() }} user{{ $users->count() !== 1 ? 's' : '' }} registered</div>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-person-plus-fill me-1"></i>Add User
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0" style="font-size:.875rem;">
            <thead style="background:#f8fafc; border-bottom:2px solid #e8edf3;">
                <tr>
                    <th style="padding:.85rem 1.2rem; font-size:.72rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.7px;">#</th>
                    <th style="padding:.85rem .75rem; font-size:.72rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.7px;">Name</th>
                    <th style="padding:.85rem .75rem; font-size:.72rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.7px;">Email</th>
                    <th style="padding:.85rem .75rem; font-size:.72rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.7px;">Role</th>
                    <th style="padding:.85rem .75rem; font-size:.72rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.7px;">Joined</th>
                    <th style="padding:.85rem 1.2rem .85rem .75rem; font-size:.72rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.7px; text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td style="padding:.75rem 1.2rem; color:#94a3b8; font-size:.75rem;">{{ $user->id }}</td>
                        <td style="padding:.75rem;">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg,#f97316,#f43f5e); display:flex; align-items:center; justify-content:center; color:#fff; font-size:.75rem; font-weight:700; flex-shrink:0;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600; color:#1e293b;">{{ $user->name }}</div>
                                    @if($user->id === auth()->id())
                                        <div style="font-size:.66rem; color:#94a3b8;">(you)</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="padding:.75rem; color:#475569;">{{ $user->email }}</td>
                        <td style="padding:.75rem;">
                            @php
                                $badge = match($user->role) {
                                    'admin'  => ['bg:#fef3c7; color:#92400e;', 'bi-shield-fill-check'],
                                    'editor' => ['bg:#eff6ff; color:#1d4ed8;', 'bi-pencil-fill'],
                                    'viewer' => ['bg:#f1f5f9; color:#475569;', 'bi-eye-fill'],
                                    default  => ['bg:#f1f5f9; color:#475569;', 'bi-person-fill'],
                                };
                            @endphp
                            <span class="badge" style="padding:.35rem .65rem; border-radius:6px; font-size:.72rem; font-weight:600; {{ $badge[0] }}">
                                <i class="bi {{ $badge[1] }} me-1"></i>{{ $user->roleLabel() }}
                            </span>
                        </td>
                        <td style="padding:.75rem; color:#94a3b8; font-size:.78rem;">
                            {{ $user->created_at->format('Y/m/d') }}
                        </td>
                        <td style="padding:.75rem 1.2rem .75rem .75rem; text-align:center;">
                            <a href="{{ route('users.edit', $user) }}"
                               class="btn btn-sm btn-outline-warning"
                               style="padding:.3rem .55rem; border-radius:7px;"
                               title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete user {{ addslashes($user->name) }}? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            style="padding:.3rem .55rem; border-radius:7px;"
                                            title="Delete">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2 opacity-30"></i>
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
