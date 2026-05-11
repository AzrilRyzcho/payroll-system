@extends('layouts.app')
@section('title', 'Ubah Departemen')
@section('page-title', 'Ubah Departemen')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('departemen.index') }}">Departemen</a></li>
    <li class="breadcrumb-item active">Ubah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="page-title-section">
            <h4>Ubah Departemen</h4>
            <p>Perbarui informasi departemen</p>
        </div>

        <div class="form-card">
            @if($errors->any())
            <div class="alert alert-danger mb-4">
                @foreach($errors->all() as $error)
                    <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                @endforeach
            </div>
            @endif

            <form action="{{ route('departemen.update', $item) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="form-label">Nama Departemen <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $item->name) }}" required autofocus>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Perbarui
                    </button>
                    <a href="{{ route('departemen.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
