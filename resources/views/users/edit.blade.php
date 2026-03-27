@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('page-sub', 'Update account details and role')

@section('content')

<div class="mb-4">
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Users
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-person-gear" style="background:#eff6ff; color:#3b82f6;"></i>
                    Edit: {{ $user->name }}
                </div>
            </div>
            <div class="card-body">

                @if($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('users.update', $user) }}" novalidate>
                    @csrf
                    @method('PUT')

                    {{-- Name --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.84rem;">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                               class="form-control @error('name') is-invalid @enderror"
                               required autofocus>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.84rem;">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                               class="form-control @error('email') is-invalid @enderror"
                               required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Role --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.84rem;">Role <span class="text-danger">*</span></label>
                        @if($user->id === auth()->id())
                            {{-- Cannot change own role --}}
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <div class="form-control bg-light text-muted" style="pointer-events:none;">
                                {{ $user->roleLabel() }} <small class="text-muted">(cannot change your own role)</small>
                            </div>
                        @else
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="admin"  {{ old('role', $user->role) === 'admin'  ? 'selected' : '' }}>Administrator — full access</option>
                                <option value="editor" {{ old('role', $user->role) === 'editor' ? 'selected' : '' }}>Editor — can add &amp; edit members</option>
                                <option value="viewer" {{ old('role', $user->role) === 'viewer' ? 'selected' : '' }}>Viewer — read-only access</option>
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @endif
                    </div>

                    {{-- Password (optional) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.84rem;">New Password <span class="text-muted fw-normal">(leave blank to keep current)</span></label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Min. 8 characters — leave blank to keep unchanged">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:.84rem;">Confirm New Password</label>
                        <input type="password" name="password_confirmation"
                               class="form-control"
                               placeholder="Only required if changing password">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2-circle me-1"></i>Save Changes
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection
