@extends('layouts.app')
@section('title', 'Tempat Sampah')
@section('page-title', 'Tempat Sampah')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Karyawan</a></li>
    <li class="breadcrumb-item active">Tempat Sampah</li>
@endsection

@section('content')
<div class="page-title-section d-flex align-items-center justify-content-between">
    <div>
        <h4>Tempat Sampah</h4>
        <p>Karyawan yang telah dihapus sementara</p>
    </div>
    <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="table-card">
    <div class="table-card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Jabatan</th>
                    <th>Dihapus Pada</th>
                    <th width="200">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($karyawan as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td class="text-muted">{{ $item->email }}</td>
                    <td>{{ $item->role->name ?? '-' }}</td>
                    <td class="text-muted">{{ $item->deleted_at->format('d M Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <form action="{{ route('karyawan.pulihkan', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan
                                </button>
                            </form>
                            <form action="{{ route('karyawan.hapusPermanent', $item->id) }}" method="POST"
                                  onsubmit="return confirm('Data akan dihapus permanen dan tidak bisa dipulihkan.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash me-1"></i> Hapus Permanen
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="bi bi-trash fs-3 d-block mb-2 opacity-25"></i>
                        Tempat sampah kosong
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
