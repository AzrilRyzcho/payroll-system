@extends('layouts.app')
@section('title', 'Pengaturan Absensi')
@section('page-title', 'Pengaturan Absensi')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('absensi.index') }}">Absensi</a></li>
    <li class="breadcrumb-item active">Pengaturan</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="page-title-section">
            <h4>Pengaturan Absensi</h4>
            <p>Konfigurasi jam kerja, toleransi, dan aturan potongan</p>
        </div>

        <div class="form-card">
            <form id="formPengaturan" action="{{ route('absensi.simpanPengaturan') }}" method="POST">
                @csrf

                <h6 class="fw-600 mb-3" style="font-weight:600;color:#1e2a3a;font-size:.9rem;">
                    <i class="bi bi-clock me-2 text-primary"></i>Jam Kerja
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Jam Masuk Normal</label>
                        <input type="time" name="jam_masuk_normal" class="form-control"
                               value="{{ substr($setting->jam_masuk_normal, 0, 5) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jam Keluar Normal</label>
                        <input type="time" name="jam_keluar_normal" class="form-control"
                               value="{{ substr($setting->jam_keluar_normal, 0, 5) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Toleransi Terlambat</label>
                        <div class="input-group">
                            <input type="number" name="toleransi_menit" class="form-control"
                                   value="{{ $setting->toleransi_menit }}" min="0" max="60" required>
                            <span class="input-group-text" style="font-size:.8rem;background:#f8f9fc;border-color:#dde1e8;">menit</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Hari Kerja per Bulan</label>
                        <div class="input-group">
                            <input type="number" name="hari_kerja_sebulan" class="form-control"
                                   value="{{ $setting->hari_kerja_sebulan }}" min="1" max="31" required>
                            <span class="input-group-text" style="font-size:.8rem;background:#f8f9fc;border-color:#dde1e8;">hari</span>
                        </div>
                    </div>
                </div>

                <hr style="border-color:#eef0f4;">

                <h6 class="fw-600 mb-3 mt-3" style="font-weight:600;color:#1e2a3a;font-size:.9rem;">
                    <i class="bi bi-dash-circle me-2 text-danger"></i>Aturan Potongan
                </h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Potongan per Menit Terlambat (Rp)</label>
                        <input type="number" name="potongan_per_menit" class="form-control"
                               value="{{ $setting->potongan_per_menit }}" min="0" required>
                        <div class="form-text">Contoh: 500 = Rp 500 per menit terlambat</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Potongan Alpha per Hari (Rp)</label>
                        <input type="number" name="potongan_alpha" class="form-control"
                               value="{{ $setting->potongan_alpha }}" min="0" required>
                        <div class="form-text">Isi 0 = otomatis potong 1 hari gaji pokok</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Potongan Izin per Hari (Rp)</label>
                        <input type="number" name="potongan_izin" class="form-control"
                               value="{{ $setting->potongan_izin }}" min="0" required>
                        <div class="form-text">Isi 0 = izin tidak dipotong</div>
                    </div>
                </div>

                <div class="mt-3 p-3 rounded-3" style="background:#fff5ec;border:1px solid #fde8cc;font-size:.8rem;color:#7d4e00;">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Catatan:</strong> Perubahan pengaturan hanya berlaku untuk rekap absensi yang dihitung setelah perubahan ini disimpan.
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary" id="btnSimpanPengaturan">
                        <i class="bi bi-check-lg me-1"></i> Simpan Pengaturan
                    </button>
                    <a href="{{ route('absensi.index') }}" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('formPengaturan').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSimpanPengaturan');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

    fetch('{{ route("absensi.simpanPengaturan") }}', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const t = document.createElement('div');
            t.className = 'alert alert-success position-fixed shadow';
            t.style.cssText = 'top:20px;right:20px;z-index:9999;min-width:300px;';
            t.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i>${data.message}`;
            document.body.appendChild(t);
            setTimeout(() => t.remove(), 3000);
        }
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Simpan Pengaturan';
    });
});
</script>
@endsection
