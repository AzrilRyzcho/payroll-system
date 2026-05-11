@extends('layouts.app')
@section('title', 'Departemen')
@section('page-title', 'Departemen')
@section('breadcrumb')
    <li class="breadcrumb-item active">Departemen</li>
@endsection

@section('content')
<div class="page-title-section d-flex align-items-center justify-content-between">
    <div>
        <h4>Daftar Departemen</h4>
        <p>Kelola semua departemen perusahaan</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDepartemen" onclick="openCreateModal()">
        <i class="bi bi-plus-lg me-1"></i> Tambah Departemen
    </button>
</div>

<div class="table-card">
    <div class="table-card-body">
        <table class="table" id="tableDepartemen">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Nama Departemen</th>
                    <th>Jumlah Jabatan</th>
                    <th>Dibuat</th>
                    <th width="160">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departemen as $item)
                <tr id="row-{{ $item->id }}">
                    <td class="text-muted">{{ $departemen->firstItem() + $loop->index }}</td>
                    <td>
                        <a href="{{ route('departemen.show', $item) }}" class="fw-500">{{ $item->name }}</a>
                    </td>
                    <td>
                        <span class="badge badge-soft-blue rounded-pill">{{ $item->roles_count }} jabatan</span>
                    </td>
                    <td class="text-muted">{{ $item->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-secondary btn-sm" onclick="openEditModal({{ $item->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteDepartemen({{ $item->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="emptyRow">
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="bi bi-building fs-3 d-block mb-2 opacity-25"></i>
                        Belum ada data departemen
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $departemen->links() }}
</div>

{{-- Modal Tambah/Edit --}}
<div class="modal fade" id="modalDepartemen" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;border:1px solid #eef0f4;">
            <div class="modal-header" style="border-bottom:1px solid #eef0f4;">
                <h6 class="modal-title fw-600" id="modalTitle">Tambah Departemen</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formDepartemen">
                @csrf
                <input type="hidden" id="departemenId" name="id">
                <input type="hidden" id="formMethod" name="_method" value="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Departemen <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="departemenName" class="form-control"
                               placeholder="Contoh: Teknologi Informasi" required>
                        <div class="invalid-feedback" id="errorName"></div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #eef0f4;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSubmit">
                        <i class="bi bi-check-lg me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let editMode = false;
let editId = null;

function openCreateModal() {
    editMode = false;
    editId = null;
    document.getElementById('modalTitle').textContent = 'Tambah Departemen';
    document.getElementById('formDepartemen').reset();
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('departemenName').classList.remove('is-invalid');
}

function openEditModal(id) {
    editMode = true;
    editId = id;
    document.getElementById('modalTitle').textContent = 'Ubah Departemen';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('departemenId').value = id;

    fetch(`/departemen/${id}/edit`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('departemenName').value = data.name;
        new bootstrap.Modal(document.getElementById('modalDepartemen')).show();
    });
}

document.getElementById('formDepartemen').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

    const formData = new FormData(this);
    const url = editMode ? `/departemen/${editId}` : '/departemen';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalDepartemen')).hide();
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 800);
        }
    })
    .catch(err => {
        showToast('Terjadi kesalahan', 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Simpan';
    });
});

function deleteDepartemen(id) {
    if (!confirm('Yakin ingin menghapus departemen ini?')) return;

    fetch(`/departemen/${id}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`row-${id}`).remove();
            showToast(data.message, 'success');
        }
    });
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
    toast.style.cssText = 'top:20px;right:20px;z-index:9999;min-width:300px;';
    toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>
@endsection
