@extends('layouts.app')
@section('title', 'Detail Departemen')
@section('page-title', 'Detail Departemen')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('departemen.index') }}">Departemen</a></li>
    <li class="breadcrumb-item active">{{ $item->name }}</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-5">
        <div class="form-card">
            <h6 class="fw-700 mb-3" style="font-weight:700; color:#1e2a3a;">
                <i class="bi bi-building me-2 text-primary"></i>Informasi Departemen
            </h6>
            <table class="table table-borderless" style="font-size:.85rem;">
                <tr>
                    <td class="text-muted" width="140">Nama</td>
                    <td class="fw-600" style="font-weight:600;">{{ $item->name }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Jumlah Jabatan</td>
                    <td><span class="badge badge-soft-blue rounded-pill">{{ $item->roles->count() }}</span></td>
                </tr>
                <tr>
                    <td class="text-muted">Dibuat</td>
                    <td>{{ $item->created_at->format('d M Y') }}</td>
                </tr>
            </table>
            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('departemen.edit', $item) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i> Ubah
                </a>
                <a href="{{ route('departemen.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
            </div>
        </div>
    </div>

    @if($item->roles->count() > 0)
    <div class="col-lg-7">
        <div class="table-card">
            <div class="table-card-header">
                <h6 class="table-card-title">Jabatan di Departemen Ini</h6>
            </div>
            <div class="table-card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Jabatan</th>
                            <th>Gaji Pokok</th>
                            <th>Karyawan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item->roles as $role)
                        <tr>
                            <td><a href="{{ route('jabatan.show', $role) }}">{{ $role->name }}</a></td>
                            <td>Rp {{ number_format($role->salary, 0, ',', '.') }}</td>
                            <td><span class="badge badge-soft-green rounded-pill">{{ $role->employees->count() }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
