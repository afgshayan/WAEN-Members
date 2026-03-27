@extends('layouts.app')

@section('title', 'Add Member')
@section('page-title', 'Add New Member')
@section('page-sub', 'Fill in the details to add a new member')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('persons.index') }}" class="text-decoration-none text-muted">Members</a>
    </li>
    <li class="breadcrumb-item active">Add Member</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <div style="width:32px; height:32px; background:linear-gradient(135deg,#3b82f6,#1d4ed8);
                            border-radius:8px; display:flex; align-items:center; justify-content:center;
                            color:#fff; font-size:.85rem; flex-shrink:0;">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <span>New Member Details</span>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('persons.store') }}" novalidate autocomplete="on" enctype="multipart/form-data">
                    @csrf

                    @include('persons._form')

                    <hr class="my-4" style="border-color:#f3f4f6;">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('persons.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Save Member
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
