@extends('layouts.app')
@section('title', 'Tambah Jabatan')
@section('page-title', 'Tambah Jabatan')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('jabatan.index') }}">Jabatan</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="page-title-section">
            <h4>Tambah Jabatan</h4>
            <p>Isi form berikut untuk menambahkan jabatan baru</p>
        </div>

        <div class="form-card">
            @if($errors->any())
            <div class="alert alert-danger mb-4">
                @foreach($errors->all() as $error)
                    <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                @endforeach
            </div>
            @endif

            <form action="{{ route('jabatan.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                           placeholder="Contoh: Software Engineer"
                           value="{{ old('name') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Departemen <span class="text-danger">*</span></label>
                    <select name="department_id" class="form-select" required>
                        <option value="">-- Pilih Departemen --</option>
                        @foreach($departemen as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label">Gaji Pokok (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="salary" class="form-control"
                           placeholder="Contoh: 5000000"
                           value="{{ old('salary') }}" min="0" required>
                    <div class="form-text">Masukkan nominal gaji pokok per bulan</div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Simpan
                    </button>
                    <a href="{{ route('jabatan.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
