@extends('layouts.app')
@section('title', 'Absensi')
@section('page-title', 'Absensi')
@section('breadcrumb')
    <li class="breadcrumb-item active">Absensi</li>
@endsection

@section('content')
<div class="page-title-section d-flex align-items-center justify-content-between">
    <div>
        <h4>Data Absensi</h4>
        <p>Pencatatan kehadiran karyawan harian</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('absensi.pengaturan') }}" class="btn btn-outline-secondary">
            <i class="bi bi-gear me-1"></i> Pengaturan
        </a>
        <button class="btn btn-primary" onclick="openCreateAbsensi()">
            <i class="bi bi-plus-lg me-1"></i> Catat Absensi
        </button>
    </div>
</div>

{{-- Filter --}}
<div class="form-card mb-3 py-3">
    <form method="GET" action="{{ route('absensi.index') }}" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Karyawan</label>
            <select name="karyawan_id" class="form-select form-select-sm">
                <option value="">Semua Karyawan</option>
                @foreach($karyawan as $k)
                <option value="{{ $k->id }}" {{ request('karyawan_id') == $k->id ? 'selected' : '' }}>
                    {{ $k->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Bulan</label>
            <select name="bulan" class="form-select form-select-sm">
                @foreach(range(1,12) as $b)
                <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Tahun</label>
            <select name="tahun" class="form-select form-select-sm">
                @foreach(range(now()->year, now()->year - 3) as $t)
                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm w-100">
                <i class="bi bi-search me-1"></i> Filter
            </button>
        </div>
    </form>
</div>

{{-- Ringkasan status bulan ini --}}
@php
    $totalHadir    = $absensi->getCollection()->whereIn('status', ['hadir','terlambat'])->count();
    $totalAlpha    = $absensi->getCollection()->where('status', 'alpha')->count();
    $totalIzin     = $absensi->getCollection()->whereIn('status', ['izin','sakit'])->count();
    $totalTerlambat = $absensi->getCollection()->where('status', 'terlambat')->count();
@endphp
<div class="row g-2 mb-3">
    <div class="col-6 col-md-3">
        <div class="stat-card py-3">
            <div class="stat-icon icon-green"><i class="bi bi-check-circle"></i></div>
            <div><div class="stat-label">Hadir</div><div class="stat-value">{{ $totalHadir }}</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card py-3">
            <div class="stat-icon icon-orange"><i class="bi bi-clock-history"></i></div>
            <div><div class="stat-label">Terlambat</div><div class="stat-value">{{ $totalTerlambat }}</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card py-3">
            <div class="stat-icon icon-blue"><i class="bi bi-calendar-x"></i></div>
            <div><div class="stat-label">Izin/Sakit</div><div class="stat-value">{{ $totalIzin }}</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card py-3">
            <div class="stat-icon" style="background:#fff0f0;color:#e74c3c;"><i class="bi bi-x-circle"></i></div>
            <div><div class="stat-label">Alpha</div><div class="stat-value">{{ $totalAlpha }}</div></div>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-body">
        <table class="table" id="tableAbsensi">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Karyawan</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Status</th>
                    <th>Terlambat</th>
                    <th>Lembur</th>
                    <th>Keterangan</th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absensi as $item)
                <tr id="row-absensi-{{ $item->id }}">
                    <td>{{ $item->tanggal->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('karyawan.show', $item->employee) }}" class="fw-500">
                            {{ $item->employee->name }}
                        </a>
                    </td>
                    <td>{{ $item->jam_masuk ? substr($item->jam_masuk, 0, 5) : '-' }}</td>
                    <td>{{ $item->jam_keluar ? substr($item->jam_keluar, 0, 5) : '-' }}</td>
                    <td>
                        <span class="badge {{ $item->warna_badge }} rounded-pill">
                            {{ $item->label_status }}
                        </span>
                    </td>
                    <td>
                        @if($item->menit_terlambat > 0)
                            <span class="badge badge-soft-orange rounded-pill">
                                {{ $item->menit_terlambat }} menit
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($item->jam_lembur > 0)
                            <span class="badge badge-soft-blue rounded-pill">
                                {{ $item->jam_lembur }} jam
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-muted" style="font-size:.78rem;">
                        {{ $item->keterangan ?? '-' }}
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-secondary btn-sm" onclick="openEditAbsensi({{ $item->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteAbsensi({{ $item->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-calendar-x fs-3 d-block mb-2 opacity-25"></i>
                        Belum ada data absensi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $absensi->links() }}
</div>

{{-- Modal Tambah/Edit Absensi --}}
<div class="modal fade" id="modalAbsensi" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:12px;border:1px solid #eef0f4;">
            <div class="modal-header" style="border-bottom:1px solid #eef0f4;">
                <h6 class="modal-title fw-600" id="modalAbsensiTitle">Catat Absensi</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAbsensi">
                @csrf
                <input type="hidden" id="absensiId">
                <input type="hidden" id="absensiMethod" value="POST">
                <div class="modal-body">
                    <div id="absensiErrors" class="alert alert-danger d-none mb-3"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                            <select name="employee_id" id="absensiKaryawan" class="form-select" required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($karyawan as $k)
                                <option value="{{ $k->id }}">{{ $k->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" id="absensiTanggal" class="form-control"
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jam Masuk</label>
                            <input type="time" name="jam_masuk" id="absensiJamMasuk" class="form-control">
                            <div class="form-text">Normal: {{ substr($setting->jam_masuk_normal, 0, 5) }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jam Keluar</label>
                            <input type="time" name="jam_keluar" id="absensiJamKeluar" class="form-control">
                            <div class="form-text">Normal: {{ substr($setting->jam_keluar_normal, 0, 5) }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="absensiStatus" class="form-select" required>
                                <option value="hadir">Hadir</option>
                                <option value="terlambat">Terlambat</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="alpha">Alpha</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" id="absensiKeterangan" class="form-control" rows="2"
                                      placeholder="Opsional — alasan izin, sakit, dll."></textarea>
                        </div>
                    </div>

                    {{-- Info otomatis --}}
                    <div id="infoOtomatis" class="mt-3 p-3 rounded-3 d-none"
                         style="background:#f8f9fc;border:1px solid #eef0f4;font-size:.8rem;">
                        <div class="fw-600 mb-2" style="color:#1e2a3a;">Kalkulasi Otomatis</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <span class="text-muted">Keterlambatan:</span>
                                <span id="infoTerlambat" class="fw-600 ms-1">-</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted">Jam Lembur:</span>
                                <span id="infoLembur" class="fw-600 ms-1">-</span>
                            </div>
                        </div>
                        <div class="mt-2" style="color:#8a9bb0;font-size:.72rem;">
                            <i class="bi bi-info-circle me-1"></i>
                            Lembur dihitung jika pulang setelah {{ substr($setting->jam_keluar_normal, 0, 5) }}
                            dan masuk tidak lebih dari 4 jam setelah jam masuk normal.
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #eef0f4;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnAbsensiSubmit">
                        <i class="bi bi-check-lg me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.badge-soft-red { background:#fff0f0; color:#e74c3c; font-weight:500; }
</style>

<script>
const JAM_MASUK_NORMAL  = '{{ substr($setting->jam_masuk_normal, 0, 5) }}';
const JAM_KELUAR_NORMAL = '{{ substr($setting->jam_keluar_normal, 0, 5) }}';
const TOLERANSI         = {{ $setting->toleransi_menit }};

let absensiEditMode = false;
let absensiEditId   = null;

function openCreateAbsensi() {
    absensiEditMode = false;
    absensiEditId   = null;
    document.getElementById('modalAbsensiTitle').textContent = 'Catat Absensi';
    document.getElementById('formAbsensi').reset();
    document.getElementById('absensiTanggal').value = new Date().toISOString().split('T')[0];
    document.getElementById('absensiErrors').classList.add('d-none');
    document.getElementById('infoOtomatis').classList.add('d-none');
    new bootstrap.Modal(document.getElementById('modalAbsensi')).show();
}

function openEditAbsensi(id) {
    absensiEditMode = true;
    absensiEditId   = id;
    document.getElementById('modalAbsensiTitle').textContent = 'Ubah Absensi';
    document.getElementById('absensiErrors').classList.add('d-none');

    fetch(`/absensi/${id}/edit`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        document.getElementById('absensiKaryawan').value   = d.employee_id;
        document.getElementById('absensiTanggal').value    = d.tanggal;
        document.getElementById('absensiJamMasuk').value   = d.jam_masuk;
        document.getElementById('absensiJamKeluar').value  = d.jam_keluar;
        document.getElementById('absensiStatus').value     = d.status;
        document.getElementById('absensiKeterangan').value = d.keterangan ?? '';
        hitungOtomatis();
        new bootstrap.Modal(document.getElementById('modalAbsensi')).show();
    });
}

// Hitung keterlambatan & lembur secara real-time
function hitungOtomatis() {
    const jamMasuk  = document.getElementById('absensiJamMasuk').value;
    const jamKeluar = document.getElementById('absensiJamKeluar').value;

    if (!jamMasuk && !jamKeluar) {
        document.getElementById('infoOtomatis').classList.add('d-none');
        return;
    }

    document.getElementById('infoOtomatis').classList.remove('d-none');

    // Hitung terlambat
    if (jamMasuk) {
        const [jm, mm]   = jamMasuk.split(':').map(Number);
        const [jn, mn]   = JAM_MASUK_NORMAL.split(':').map(Number);
        const menitMasuk  = jm * 60 + mm;
        const menitNormal = jn * 60 + mn;
        // Terlambat = selisih dari jam masuk normal (bukan + toleransi, toleransi hanya untuk status)
        const terlambat   = Math.max(0, menitMasuk - menitNormal);
        const melebihiToleransi = terlambat > TOLERANSI;

        if (terlambat === 0) {
            document.getElementById('infoTerlambat').textContent = 'Tepat waktu';
            document.getElementById('infoTerlambat').style.color = '#27ae60';
        } else if (!melebihiToleransi) {
            document.getElementById('infoTerlambat').textContent = `${terlambat} menit (dalam toleransi)`;
            document.getElementById('infoTerlambat').style.color = '#27ae60';
        } else {
            document.getElementById('infoTerlambat').textContent = `${terlambat} menit`;
            document.getElementById('infoTerlambat').style.color = '#e67e22';
        }

        // Auto set status
        const statusEl = document.getElementById('absensiStatus');
        if (melebihiToleransi && statusEl.value === 'hadir') {
            statusEl.value = 'terlambat';
        } else if (!melebihiToleransi && statusEl.value === 'terlambat') {
            statusEl.value = 'hadir';
        }
    }

    // Hitung lembur — hanya valid jika jam keluar > jam pulang normal
    // DAN jam masuk tidak terlalu jauh dari jam masuk normal (bukan shift sore)
    if (jamKeluar) {
        const [jk, mk]   = jamKeluar.split(':').map(Number);
        const [jp, mp]   = JAM_KELUAR_NORMAL.split(':').map(Number);
        const menitKeluar = jk * 60 + mk;
        const menitPulang = jp * 60 + mp;
        const lemburMenit = Math.max(0, menitKeluar - menitPulang);

        // Lembur hanya dihitung jika karyawan hadir (ada jam masuk)
        // dan jam masuk tidak lebih dari 4 jam setelah jam masuk normal
        let lemburValid = false;
        if (jamMasuk) {
            const [jm, mm] = jamMasuk.split(':').map(Number);
            const [jn, mn] = JAM_MASUK_NORMAL.split(':').map(Number);
            const selisihMasuk = (jm * 60 + mm) - (jn * 60 + mn);
            // Dianggap lembur valid jika masuk tidak lebih dari 4 jam terlambat
            lemburValid = selisihMasuk <= 240;
        }

        if (lemburMenit > 0 && lemburValid) {
            const lemburJam = (lemburMenit / 60).toFixed(2);
            document.getElementById('infoLembur').textContent = `${lemburJam} jam`;
            document.getElementById('infoLembur').style.color = '#4f8ef7';
        } else if (lemburMenit > 0 && !lemburValid) {
            document.getElementById('infoLembur').textContent = 'Tidak dihitung (masuk terlalu siang)';
            document.getElementById('infoLembur').style.color = '#8a9bb0';
        } else {
            document.getElementById('infoLembur').textContent = 'Tidak ada';
            document.getElementById('infoLembur').style.color = '#8a9bb0';
        }
    }
}

document.getElementById('absensiJamMasuk').addEventListener('change', hitungOtomatis);
document.getElementById('absensiJamKeluar').addEventListener('change', hitungOtomatis);

document.getElementById('formAbsensi').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnAbsensiSubmit');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

    const formData = new FormData(this);
    const url = absensiEditMode ? `/absensi/${absensiEditId}` : '/absensi';
    if (absensiEditMode) formData.append('_method', 'PUT');

    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalAbsensi')).hide();
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            const errDiv = document.getElementById('absensiErrors');
            errDiv.innerHTML = `<i class="bi bi-exclamation-circle me-1"></i>${data.message}`;
            errDiv.classList.remove('d-none');
        }
    })
    .catch(() => showToast('Terjadi kesalahan', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Simpan';
    });
});

function deleteAbsensi(id) {
    if (!confirm('Yakin ingin menghapus data absensi ini?')) return;
    fetch(`/absensi/${id}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`row-absensi-${id}`).remove();
            showToast(data.message, 'success');
        }
    });
}

function showToast(message, type) {
    const t = document.createElement('div');
    t.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed shadow`;
    t.style.cssText = 'top:20px;right:20px;z-index:9999;min-width:300px;';
    t.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill'} me-2"></i>${message}`;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}
</script>
@endsection
