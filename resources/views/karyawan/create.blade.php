@extends('layouts.app')
@section('title', 'Tambah Karyawan')
@section('page-title', 'Tambah Karyawan')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Karyawan</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="page-title-section">
            <h4>Tambah Karyawan</h4>
            <p>Isi form berikut untuk mendaftarkan karyawan baru</p>
        </div>

        <div class="form-card">
            @if($errors->any())
            <div class="alert alert-danger mb-4">
                @foreach($errors->all() as $error)
                    <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                @endforeach
            </div>
            @endif

            <form action="{{ route('karyawan.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               placeholder="Nama lengkap karyawan"
                               value="{{ old('name') }}" required autofocus>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control"
                               placeholder="email@perusahaan.com"
                               value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone" class="form-control"
                               placeholder="08xxxxxxxxxx"
                               value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                        <select name="role_id" class="form-select" required>
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($jabatan as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }} — {{ $role->department->name ?? '-' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control" rows="3"
                                  placeholder="Alamat lengkap karyawan">{{ old('address') }}</textarea>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Simpan
                    </button>
                    <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
