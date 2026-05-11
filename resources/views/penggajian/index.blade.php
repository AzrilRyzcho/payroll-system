@extends('layouts.app')
@section('title', 'Penggajian')
@section('page-title', 'Penggajian')
@section('breadcrumb')
    <li class="breadcrumb-item active">Penggajian</li>
@endsection

@section('content')
<div class="page-title-section d-flex align-items-center justify-content-between">
    <div>
        <h4>Data Penggajian</h4>
        <p>Riwayat dan pengelolaan gaji karyawan</p>
    </div>
    <a href="{{ route('penggajian.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Penggajian
    </a>
</div>

<div class="table-card">
    <div class="table-card-body">
        <table class="table">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Karyawan</th>
                    <th>Tanggal</th>
                    <th>Hari Kerja</th>
                    <th>Lembur</th>
                    <th>Bonus</th>
                    <th>Potongan</th>
                    <th>Total Gaji</th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penggajian as $item)
                <tr>
                    <td class="text-muted">{{ $penggajian->firstItem() + $loop->index }}</td>
                    <td>
                        <a href="{{ route('karyawan.show', $item->employee) }}" class="fw-500">
                            {{ $item->employee->name }}
                        </a>
                        <div class="text-muted" style="font-size:.72rem;">{{ $item->employee->role->name ?? '-' }}</div>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->payroll_date)->format('d M Y') }}</td>
                    <td>{{ $item->work_days }} hari</td>
                    <td>
                        @if($item->overtime_hours > 0)
                            <span class="badge badge-soft-orange rounded-pill">{{ $item->overtime_hours }} jam</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>Rp {{ number_format($item->bonus, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->deduction, 0, ',', '.') }}</td>
                    <td>
                        <span style="font-weight:700;color:#27ae60;">
                            Rp {{ number_format($item->total_salary, 0, ',', '.') }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('penggajian.edit', $item) }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('penggajian.destroy', $item) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus data penggajian ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-cash-stack fs-3 d-block mb-2 opacity-25"></i>
                        Belum ada data penggajian
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $penggajian->links() }}
</div>
@endsection
