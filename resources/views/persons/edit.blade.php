@extends('layouts.app')

@section('title', 'Edit Member')
@section('page-title', 'Edit Member')
@section('page-sub', 'Update the details for ' . $person->first_name . ' ' . $person->last_name)
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('persons.index') }}" class="text-decoration-none text-muted">Members</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('persons.show', $person) }}" class="text-decoration-none text-muted">
            {{ $person->first_name }} {{ $person->last_name }}
        </a>
    </li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:32px; height:32px; background:linear-gradient(135deg,#f59e0b,#d97706);
                                border-radius:8px; display:flex; align-items:center; justify-content:center;
                                color:#fff; font-size:.85rem; flex-shrink:0;">
                        <i class="bi bi-pencil-fill"></i>
                    </div>
                    <span>Editing: {{ $person->first_name }} {{ $person->last_name }}</span>
                </div>
                <span class="badge bg-light text-secondary border" style="font-size:.72rem;">#{{ $person->id }}</span>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('persons.update', $person) }}" novalidate autocomplete="on" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('persons._form')

                    <hr class="my-4" style="border-color:#f3f4f6;">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('persons.show', $person) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn" style="background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; border:none;">
                            <i class="bi bi-check-circle me-1"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
