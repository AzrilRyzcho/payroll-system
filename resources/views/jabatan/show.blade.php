@extends('layouts.app')
@section('title', 'Detail Jabatan')
@section('page-title', 'Detail Jabatan')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('jabatan.index') }}">Jabatan</a></li>
    <li class="breadcrumb-item active">{{ $item->name }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="form-card">
            <h6 class="fw-700 mb-3" style="font-weight:700; color:#1e2a3a;">
                <i class="bi bi-briefcase me-2 text-primary"></i>Informasi Jabatan
            </h6>
            <table class="table table-borderless" style="font-size:.85rem;">
                <tr>
                    <td class="text-muted" width="140">Nama Jabatan</td>
                    <td class="fw-600" style="font-weight:600;">{{ $item->name }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Departemen</td>
                    <td>{{ $item->department->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Gaji Pokok</td>
                    <td class="fw-600" style="font-weight:600; color:#27ae60;">
                        Rp {{ number_format($item->salary, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">Jumlah Karyawan</td>
                    <td><span class="badge badge-soft-blue rounded-pill">{{ $item->employees->count() }} orang</span></td>
                </tr>
            </table>
            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('jabatan.edit', $item) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i> Ubah
                </a>
                <a href="{{ route('jabatan.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection
