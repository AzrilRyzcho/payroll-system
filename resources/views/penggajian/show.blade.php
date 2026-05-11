@extends('layouts.app')
@section('title', 'Detail Penggajian')
@section('page-title', 'Detail Penggajian')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('penggajian.index') }}">Penggajian</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="form-card">
            <h6 class="fw-700 mb-4" style="font-weight:700;color:#1e2a3a;">
                <i class="bi bi-receipt me-2 text-success"></i>Slip Penggajian
            </h6>

            <table class="table table-borderless" style="font-size:.85rem;">
                <tr>
                    <td class="text-muted" width="160">Karyawan</td>
                    <td class="fw-600" style="font-weight:600;">{{ $item->employee->name }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Jabatan</td>
                    <td>{{ $item->employee->role->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Departemen</td>
                    <td>{{ $item->employee->role->department->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Tanggal</td>
                    <td>{{ \Carbon\Carbon::parse($item->payroll_date)->format('d M Y') }}</td>
                </tr>
            </table>

            <hr style="border-color:#eef0f4;">

            <table class="table table-borderless" style="font-size:.85rem;">
                <tr>
                    <td class="text-muted">Gaji Pokok</td>
                    <td>Rp {{ number_format($item->employee->role->salary ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Hari Kerja</td>
                    <td>{{ $item->work_days }} hari</td>
                </tr>
                <tr>
                    <td class="text-muted">Jam Lembur</td>
                    <td>{{ $item->overtime_hours }} jam</td>
                </tr>
                <tr>
                    <td class="text-muted">Bonus</td>
                    <td>+ Rp {{ number_format($item->bonus, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Potongan</td>
                    <td>− Rp {{ number_format($item->deduction, 0, ',', '.') }}</td>
                </tr>
            </table>

            <div class="p-3 rounded-3 mt-2" style="background:linear-gradient(135deg,#edfaf4,#d5f5e3);">
                <div style="font-size:.75rem;color:#27ae60;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">
                    Total Gaji Diterima
                </div>
                <div style="font-size:1.5rem;font-weight:700;color:#1a7a45;margin-top:4px;">
                    Rp {{ number_format($item->total_salary, 0, ',', '.') }}
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('penggajian.edit', $item) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i> Ubah
                </a>
                <a href="{{ route('penggajian.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection
