@extends('layouts.app')
@section('title', 'Detail Karyawan')
@section('page-title', 'Detail Karyawan')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Karyawan</a></li>
    <li class="breadcrumb-item active">{{ $item->name }}</li>
@endsection

@section('content')
<div class="row g-3">
    {{-- Info Card --}}
    <div class="col-lg-4">
        <div class="form-card text-center">
            @php
                $colors = ['#4f8ef7','#7c5cfc','#27ae60','#e67e22'];
                $color  = $colors[$item->id % count($colors)];
            @endphp
            <div style="width:64px;height:64px;border-radius:50%;background:{{ $color }};
                        display:flex;align-items:center;justify-content:center;
                        font-size:1.5rem;font-weight:700;color:#fff;margin:0 auto 12px;">
                {{ strtoupper(substr($item->name, 0, 1)) }}
            </div>
            <h6 style="font-weight:700;color:#1e2a3a;">{{ $item->name }}</h6>
            <p class="text-muted mb-3" style="font-size:.8rem;">{{ $item->email }}</p>

            <div class="d-flex justify-content-center gap-2 mb-4">
                <span class="badge badge-soft-blue rounded-pill">{{ $item->role->name ?? '-' }}</span>
                <span class="badge badge-soft-orange rounded-pill">{{ $item->role->department->name ?? '-' }}</span>
            </div>

            <table class="table table-borderless text-start" style="font-size:.82rem;">
                <tr>
                    <td class="text-muted">Telepon</td>
                    <td>{{ $item->phone ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Gaji Pokok</td>
                    <td class="fw-600" style="font-weight:600;color:#27ae60;">
                        Rp {{ number_format($item->role->salary ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">Alamat</td>
                    <td>{{ $item->address ?? '-' }}</td>
                </tr>
            </table>

            <div class="d-flex gap-2 justify-content-center mt-2">
                <a href="{{ route('karyawan.edit', $item) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i> Ubah
                </a>
                <a href="{{ route('penggajian.create', ['karyawan_id' => $item->id]) }}"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-cash me-1"></i> Gaji
                </a>
            </div>
        </div>
    </div>

    {{-- Riwayat Penggajian --}}
    <div class="col-lg-8">
        <div class="table-card">
            <div class="table-card-header">
                <h6 class="table-card-title">
                    <i class="bi bi-receipt me-2 text-success"></i>Riwayat Penggajian
                </h6>
                <a href="{{ route('penggajian.create', ['karyawan_id' => $item->id]) }}"
                   class="btn btn-primary btn-sm" style="font-size:.75rem;padding:5px 12px;">
                    <i class="bi bi-plus-lg me-1"></i> Tambah
                </a>
            </div>
            <div class="table-card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Hari Kerja</th>
                            <th>Lembur</th>
                            <th>Bonus</th>
                            <th>Potongan</th>
                            <th>Total Gaji</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($item->payrolls->sortByDesc('payroll_date') as $payroll)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($payroll->payroll_date)->format('d M Y') }}</td>
                            <td>{{ $payroll->work_days }} hari</td>
                            <td>{{ $payroll->overtime_hours }} jam</td>
                            <td>Rp {{ number_format($payroll->bonus, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($payroll->deduction, 0, ',', '.') }}</td>
                            <td>
                                <span style="font-weight:600;color:#27ae60;">
                                    Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4 d-block mb-1 opacity-25"></i>
                                Belum ada riwayat penggajian
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
