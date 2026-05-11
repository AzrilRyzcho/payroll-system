@extends('layouts.app')
@section('title', 'Ubah Karyawan')
@section('page-title', 'Ubah Karyawan')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Karyawan</a></li>
    <li class="breadcrumb-item active">Ubah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="page-title-section">
            <h4>Ubah Data Karyawan</h4>
            <p>Perbarui informasi karyawan</p>
        </div>

        <div class="form-card">
            @if($errors->any())
            <div class="alert alert-danger mb-4">
                @foreach($errors->all() as $error)
                    <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                @endforeach
            </div>
            @endif

            <form action="{{ route('karyawan.update', $item) }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', $item->name) }}" required autofocus>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $item->email) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone', $item->phone) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                        <select name="role_id" class="form-select" required>
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($jabatan as $role)
                            <option value="{{ $role->id }}"
                                {{ old('role_id', $item->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->name }} — {{ $role->department->name ?? '-' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control" rows="3">{{ old('address', $item->address) }}</textarea>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Perbarui
                    </button>
                    <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
