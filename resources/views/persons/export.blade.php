@extends('layouts.app')

@section('title', 'Export Members')
@section('page-title', 'Export Members')
@section('page-sub', 'Apply filters, then download a CSV of matching members')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('persons.index') }}" class="text-decoration-none text-muted">Members</a>
    </li>
    <li class="breadcrumb-item active">Export CSV</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <div style="width:32px; height:32px; background:linear-gradient(135deg,#16a34a,#15803d);
                            border-radius:8px; display:flex; align-items:center; justify-content:center;
                            color:#fff; font-size:.85rem; flex-shrink:0;">
                    <i class="bi bi-download"></i>
                </div>
                <span>Export Filters</span>
            </div>
            <div class="card-body p-4">

                <div class="alert d-flex gap-3 mb-4"
                     style="background:#f0fdf4; color:#166534; border:none; border-radius:10px;">
                    <i class="bi bi-info-circle-fill fs-5 mt-1 flex-shrink-0"></i>
                    <div style="font-size:.85rem;">
                        All filters are <strong>optional</strong>. Leave them blank to export the full member list.
                        The CSV includes all fields and is UTF-8 encoded (Excel compatible).
                    </div>
                </div>

                <form method="POST" action="{{ route('persons.export.download') }}" novalidate>
                    @csrf

                    <div class="row g-3">

                        {{-- Search --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">
                                <i class="bi bi-search me-1 text-muted"></i>Search
                            </label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Name, email, phone, event …"
                                   value="{{ old('search') }}" maxlength="200">
                        </div>

                        {{-- Province --}}
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Province</label>
                            <select name="province" class="form-select">
                                <option value="">All Provinces</option>
                                @foreach($provinces as $prov)
                                    <option value="{{ $prov }}" {{ old('province') === $prov ? 'selected' : '' }}>
                                        {{ $prov }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Country --}}
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Country</label>
                            <select name="country" class="form-select">
                                <option value="">All Countries</option>
                                @foreach($countries as $co)
                                    <option value="{{ $co }}" {{ old('country') === $co ? 'selected' : '' }}>
                                        {{ $co }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Event --}}
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Event Name</label>
                            <select name="event_name" class="form-select">
                                <option value="">All Events</option>
                                @foreach($events as $ev)
                                    <option value="{{ $ev }}" {{ old('event_name') === $ev ? 'selected' : '' }}>
                                        {{ $ev }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Education --}}
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Education</label>
                            <select name="education" class="form-select">
                                <option value="">All Levels</option>
                                @foreach($educations as $edu)
                                    <option value="{{ $edu }}" {{ old('education') === $edu ? 'selected' : '' }}>
                                        {{ $edu }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Gender --}}
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">All</option>
                                @foreach(['Male','Female','Other'] as $g)
                                    <option value="{{ $g }}" {{ old('gender') === $g ? 'selected' : '' }}>
                                        {{ $g }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <hr style="border-color:#f3f4f6; margin:1.5rem 0 1rem;">

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('persons.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to List
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-download me-1"></i>Download CSV
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection
