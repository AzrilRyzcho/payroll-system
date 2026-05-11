@extends('layouts.app')
@section('title', 'Jabatan')
@section('page-title', 'Jabatan')
@section('breadcrumb')
    <li class="breadcrumb-item active">Jabatan</li>
@endsection

@section('content')
<div class="page-title-section d-flex align-items-center justify-content-between">
    <div>
        <h4>Daftar Jabatan</h4>
        <p>Kelola posisi dan gaji pokok karyawan</p>
    </div>
    <a href="{{ route('jabatan.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Jabatan
    </a>
</div>

<div class="table-card">
    <div class="table-card-body">
        <table class="table">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Nama Jabatan</th>
                    <th>Departemen</th>
                    <th>Gaji Pokok</th>
                    <th>Karyawan</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jabatan as $item)
                <tr>
                    <td class="text-muted">{{ $jabatan->firstItem() + $loop->index }}</td>
                    <td><a href="{{ route('jabatan.show', $item) }}" class="fw-500">{{ $item->name }}</a></td>
                    <td>
                        <span class="badge badge-soft-orange rounded-pill">
                            {{ $item->department->name ?? '-' }}
                        </span>
                    </td>
                    <td class="fw-600" style="font-weight:600; color:#27ae60;">
                        Rp {{ number_format($item->salary, 0, ',', '.') }}
                    </td>
                    <td>
                        <span class="badge badge-soft-blue rounded-pill">
                            {{ $item->employees_count ?? $item->employees->count() }} orang
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('jabatan.edit', $item) }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('jabatan.destroy', $item) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus jabatan ini?')">
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
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-briefcase fs-3 d-block mb-2 opacity-25"></i>
                        Belum ada data jabatan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $jabatan->links() }}
</div>
@endsection
