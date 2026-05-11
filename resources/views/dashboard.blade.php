@extends('layouts.app')

@section('title', 'Dashboard — Sistem Gaji')
@section('page-title', 'Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon icon-blue">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div>
                <div class="stat-label">Total Penggajian</div>
                <div class="stat-value">{{ $totalPenggajian }}</div>
                <div class="stat-desc">Slip gaji diterbitkan</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon icon-purple">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="stat-label">Total Karyawan</div>
                <div class="stat-value">{{ $totalKaryawan }}</div>
                <div class="stat-desc">Karyawan aktif</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon icon-green">
                <i class="bi bi-briefcase-fill"></i>
            </div>
            <div>
                <div class="stat-label">Total Jabatan</div>
                <div class="stat-value">{{ $totalJabatan }}</div>
                <div class="stat-desc">Posisi tersedia</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon icon-orange">
                <i class="bi bi-building-fill"></i>
            </div>
            <div>
                <div class="stat-label">Total Departemen</div>
                <div class="stat-value">{{ $totalDepartemen }}</div>
                <div class="stat-desc">Divisi aktif</div>
            </div>
        </div>
    </div>
</div>

{{-- Tables row --}}
<div class="row g-3">

    {{-- Karyawan Terbaru --}}
    <div class="col-xl-7">
        <div class="table-card">
            <div class="table-card-header">
                <h6 class="table-card-title">
                    <i class="bi bi-person-lines-fill me-2 text-primary"></i>Karyawan Terbaru
                </h6>
                <a href="{{ route('karyawan.index') }}" class="btn btn-secondary btn-sm" style="font-size:.75rem; padding:5px 12px;">
                    Lihat Semua
                </a>
            </div>
            <div class="table-card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Departemen</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($karyawanTerbaru as $k)
                        @php
                            $colors = ['#4f8ef7','#7c5cfc','#27ae60','#e67e22','#e74c3c'];
                            $color  = $colors[$loop->index % count($colors)];
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm" style="background:{{ $color }}">
                                        {{ strtoupper(substr($k->name, 0, 1)) }}
                                    </div>
                                    <a href="{{ route('karyawan.show', $k) }}">{{ $k->name }}</a>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-soft-blue rounded-pill">
                                    {{ $k->role->name ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $k->role->department->name ?? '-' }}</td>
                            <td>{{ $k->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                Belum ada data karyawan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Penggajian Terbaru --}}
    <div class="col-xl-5">
        <div class="table-card">
            <div class="table-card-header">
                <h6 class="table-card-title">
                    <i class="bi bi-receipt me-2 text-success"></i>Penggajian Terbaru
                </h6>
                <a href="{{ route('penggajian.index') }}" class="btn btn-secondary btn-sm" style="font-size:.75rem; padding:5px 12px;">
                    Lihat Semua
                </a>
            </div>
            <div class="table-card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Karyawan</th>
                            <th>Tanggal</th>
                            <th>Total Gaji</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penggajianTerbaru as $p)
                        <tr>
                            <td>
                                <a href="{{ route('karyawan.show', $p->employee) }}">
                                    {{ $p->employee->name }}
                                </a>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($p->payroll_date)->format('d M Y') }}</td>
                            <td>
                                <span class="fw-600" style="color:#27ae60; font-weight:600;">
                                    Rp {{ number_format($p->total_salary, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                Belum ada penggajian
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
