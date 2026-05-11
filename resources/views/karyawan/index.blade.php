@extends('layouts.app')
@section('title', 'Karyawan')
@section('page-title', 'Karyawan')
@section('breadcrumb')
    <li class="breadcrumb-item active">Karyawan</li>
@endsection

@section('content')
<div class="page-title-section d-flex align-items-center justify-content-between">
    <div>
        <h4>Daftar Karyawan</h4>
        <p>Kelola data seluruh karyawan perusahaan</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('karyawan.sampah') }}" class="btn btn-outline-secondary">
            <i class="bi bi-trash me-1"></i> Tempat Sampah
        </a>
        <button class="btn btn-primary" onclick="openCreateKaryawan()">
            <i class="bi bi-plus-lg me-1"></i> Tambah Karyawan
        </button>
    </div>
</div>

<div class="table-card">
    <div class="table-card-body">
        <table class="table" id="tableKaryawan">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Jabatan</th>
                    <th>Departemen</th>
                    <th width="160">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($karyawan as $item)
                @php
                    $colors = ['#4f8ef7','#7c5cfc','#27ae60','#e67e22','#e74c3c'];
                    $color  = $colors[$loop->index % count($colors)];
                @endphp
                <tr id="row-karyawan-{{ $item->id }}">
                    <td class="text-muted">{{ $karyawan->firstItem() + $loop->index }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-sm" style="background:{{ $color }}">
                                {{ strtoupper(substr($item->name, 0, 1)) }}
                            </div>
                            <a href="{{ route('karyawan.show', $item) }}" class="fw-500">{{ $item->name }}</a>
                        </div>
                    </td>
                    <td class="text-muted">{{ $item->email }}</td>
                    <td>
                        <span class="badge badge-soft-blue rounded-pill">
                            {{ $item->role->name ?? '-' }}
                        </span>
                    </td>
                    <td>{{ $item->role->department->name ?? '-' }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-secondary btn-sm" onclick="openEditKaryawan({{ $item->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteKaryawan({{ $item->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                            <a href="{{ route('penggajian.create', ['karyawan_id' => $item->id]) }}"
                               class="btn btn-outline-secondary btn-sm" title="Penggajian">
                                <i class="bi bi-cash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-people fs-3 d-block mb-2 opacity-25"></i>
                        Belum ada data karyawan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $karyawan->links() }}
</div>

{{-- Modal Tambah/Edit Karyawan --}}
<div class="modal fade" id="modalKaryawan" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:12px;border:1px solid #eef0f4;">
            <div class="modal-header" style="border-bottom:1px solid #eef0f4;">
                <h6 class="modal-title fw-600" id="modalKaryawanTitle">Tambah Karyawan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formKaryawan">
                @csrf
                <input type="hidden" id="karyawanId" name="id">
                <input type="hidden" id="karyawanMethod" name="_method" value="POST">
                <div class="modal-body">
                    <div id="karyawanErrors" class="alert alert-danger d-none mb-3"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="karyawanName" class="form-control"
                                   placeholder="Nama lengkap karyawan" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="karyawanEmail" class="form-control"
                                   placeholder="email@perusahaan.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="phone" id="karyawanPhone" class="form-control"
                                   placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                            <select name="role_id" id="karyawanRole" class="form-select" required>
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach(\App\Models\Role::with('department')->orderBy('name')->get() as $role)
                                <option value="{{ $role->id }}">
                                    {{ $role->name }} — {{ $role->department->name ?? '-' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" id="karyawanAddress" class="form-control" rows="2"
                                      placeholder="Alamat lengkap karyawan"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #eef0f4;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnKaryawanSubmit">
                        <i class="bi bi-check-lg me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let karyawanEditMode = false;
let karyawanEditId = null;

function openCreateKaryawan() {
    karyawanEditMode = false;
    karyawanEditId = null;
    document.getElementById('modalKaryawanTitle').textContent = 'Tambah Karyawan';
    document.getElementById('formKaryawan').reset();
    document.getElementById('karyawanMethod').value = 'POST';
    document.getElementById('karyawanErrors').classList.add('d-none');
    new bootstrap.Modal(document.getElementById('modalKaryawan')).show();
}

function openEditKaryawan(id) {
    karyawanEditMode = true;
    karyawanEditId = id;
    document.getElementById('modalKaryawanTitle').textContent = 'Ubah Karyawan';
    document.getElementById('karyawanMethod').value = 'PUT';
    document.getElementById('karyawanId').value = id;
    document.getElementById('karyawanErrors').classList.add('d-none');

    fetch(`/karyawan/${id}/edit`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('karyawanName').value    = data.name;
        document.getElementById('karyawanEmail').value   = data.email;
        document.getElementById('karyawanPhone').value   = data.phone ?? '';
        document.getElementById('karyawanRole').value    = data.role_id;
        document.getElementById('karyawanAddress').value = data.address ?? '';
        new bootstrap.Modal(document.getElementById('modalKaryawan')).show();
    });
}

document.getElementById('formKaryawan').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnKaryawanSubmit');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

    const formData = new FormData(this);
    const url = karyawanEditMode ? `/karyawan/${karyawanEditId}` : '/karyawan';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalKaryawan')).hide();
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 800);
        } else if (data.errors) {
            const errDiv = document.getElementById('karyawanErrors');
            errDiv.innerHTML = Object.values(data.errors).flat().map(e => `<div>${e}</div>`).join('');
            errDiv.classList.remove('d-none');
        }
    })
    .catch(() => showToast('Terjadi kesalahan', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Simpan';
    });
});

function deleteKaryawan(id) {
    if (!confirm('Karyawan akan dipindahkan ke tempat sampah.')) return;

    fetch(`/karyawan/${id}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`row-karyawan-${id}`).remove();
            showToast(data.message, 'success');
        }
    });
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed shadow`;
    toast.style.cssText = 'top:20px;right:20px;z-index:9999;min-width:300px;animation:fadeIn .3s;';
    toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill'} me-2"></i>${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>
@endsection
