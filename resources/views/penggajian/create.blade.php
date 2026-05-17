@extends('layouts.app')
@section('title', 'Tambah Penggajian')
@section('page-title', 'Tambah Penggajian')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('penggajian.index') }}">Penggajian</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-11">
        <div class="page-title-section">
            <h4>Tambah Penggajian</h4>
            <p>Data hari kerja, lembur, dan potongan diambil otomatis dari rekap absensi</p>
        </div>

        <div class="row g-3">
            {{-- Form --}}
            <div class="col-lg-7">
                <div class="form-card">
                    @if($errors->any())
                    <div class="alert alert-danger mb-4">
                        @foreach($errors->all() as $error)
                            <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                        @endforeach
                    </div>
                    @endif

                    <form action="{{ route('penggajian.store') }}" method="POST" id="formPenggajian">
                        @csrf

                        {{-- Pilih karyawan & periode --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                                <select name="employee_id" id="employee_id" class="form-select" required>
                                    <option value="">-- Pilih Karyawan --</option>
                                    @foreach($karyawan as $k)
                                    <option value="{{ $k->id }}"
                                        {{ old('employee_id', request('karyawan_id')) == $k->id ? 'selected' : '' }}>
                                        {{ $k->name }} — {{ $k->role->name ?? '-' }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Bulan</label>
                                <select id="bulan_absensi" class="form-select">
                                    @foreach(range(1,12) as $b)
                                    <option value="{{ $b }}" {{ now()->month == $b ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tahun</label>
                                <select id="tahun_absensi" class="form-select">
                                    @foreach(range(now()->year, now()->year - 2) as $t)
                                    <option value="{{ $t }}">{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Info rekap absensi --}}
                        <div id="rekapAbsensiInfo" class="mb-3 p-3 rounded-3 d-none"
                             style="background:#f0f7ff;border:1px solid #c8e0ff;font-size:.8rem;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-calendar-check text-primary"></i>
                                <span class="fw-600" style="font-weight:600;color:#1e2a3a;">Rekap Absensi Bulan Ini</span>
                                <div id="rekapSpinner" class="spinner-border spinner-border-sm text-primary ms-auto d-none"
                                     style="width:14px;height:14px;"></div>
                            </div>
                            <div class="row g-2" id="rekapDetail">
                                <div class="col-3 text-center">
                                    <div class="fw-700" id="rek_hadir" style="font-size:1.1rem;font-weight:700;color:#27ae60;">-</div>
                                    <div class="text-muted">Hadir</div>
                                </div>
                                <div class="col-3 text-center">
                                    <div class="fw-700" id="rek_alpha" style="font-size:1.1rem;font-weight:700;color:#e74c3c;">-</div>
                                    <div class="text-muted">Alpha</div>
                                </div>
                                <div class="col-3 text-center">
                                    <div class="fw-700" id="rek_terlambat" style="font-size:1.1rem;font-weight:700;color:#e67e22;">-</div>
                                    <div class="text-muted">Terlambat</div>
                                </div>
                                <div class="col-3 text-center">
                                    <div class="fw-700" id="rek_lembur" style="font-size:1.1rem;font-weight:700;color:#4f8ef7;">-</div>
                                    <div class="text-muted">Jam Lembur</div>
                                </div>
                            </div>
                        </div>

                        <div id="noAbsensiWarning" class="mb-3 p-3 rounded-3 d-none"
                             style="background:#fff5ec;border:1px solid #fde8cc;font-size:.8rem;color:#7d4e00;">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Belum ada data absensi untuk periode ini. Isi manual atau catat absensi terlebih dahulu.
                        </div>

                        <hr style="border-color:#eef0f4;">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Penggajian <span class="text-danger">*</span></label>
                                <input type="date" name="payroll_date" class="form-control"
                                       value="{{ old('payroll_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hari Kerja <span class="text-danger">*</span></label>
                                <input type="number" name="work_days" id="work_days" class="form-control calc-trigger"
                                       value="{{ old('work_days', 22) }}" min="0" max="31" required>
                                <div class="form-text">Diisi otomatis dari absensi</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jam Lembur</label>
                                <input type="number" name="overtime_hours" id="overtime_hours" class="form-control calc-trigger"
                                       value="{{ old('overtime_hours', 0) }}" min="0" step="0.5">
                                <div class="form-text">Diisi otomatis dari absensi</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bonus (Rp)</label>
                                <input type="number" name="bonus" id="bonus" class="form-control calc-trigger"
                                       value="{{ old('bonus', 0) }}" min="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Potongan (Rp)</label>
                                <input type="number" name="deduction" id="deduction" class="form-control calc-trigger"
                                       value="{{ old('deduction', 0) }}" min="0">
                                <div class="form-text">Diisi otomatis dari rekap keterlambatan & alpha</div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Simpan Penggajian
                            </button>
                            <a href="{{ route('penggajian.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Preview Kalkulasi --}}
            <div class="col-lg-5">
                <div class="form-card" style="position:sticky;top:80px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div style="width:32px;height:32px;background:linear-gradient(135deg,#edfaf4,#d5f5e3);
                                    border-radius:8px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-calculator" style="color:#27ae60;font-size:.9rem;"></i>
                        </div>
                        <h6 class="mb-0 fw-600" style="font-weight:600;color:#1e2a3a;">Preview Kalkulasi</h6>
                        <div id="calcSpinner" class="spinner-border spinner-border-sm text-secondary ms-auto d-none"
                             style="width:14px;height:14px;"></div>
                    </div>

                    <div id="previewEmpty" class="text-center text-muted py-3" style="font-size:.82rem;">
                        <i class="bi bi-person-circle fs-3 d-block mb-2 opacity-25"></i>
                        Pilih karyawan untuk melihat kalkulasi
                    </div>

                    <div id="previewContent" class="d-none">
                        <table class="table table-borderless mb-0" style="font-size:.82rem;">
                            <tr>
                                <td class="text-muted py-1">Gaji Pokok</td>
                                <td class="text-end py-1" id="prev_gaji_pokok">—</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-1">
                                    Gaji Kerja
                                    <span id="prev_hari_label" class="text-muted" style="font-size:.7rem;"></span>
                                </td>
                                <td class="text-end py-1" id="prev_gaji_kerja">—</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-1">Gaji Lembur</td>
                                <td class="text-end py-1" id="prev_gaji_lembur">—</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-1">Bonus</td>
                                <td class="text-end py-1" style="color:#27ae60;" id="prev_bonus">—</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-1">Potongan</td>
                                <td class="text-end py-1" style="color:#e74c3c;" id="prev_deduction">—</td>
                            </tr>
                        </table>

                        {{-- Rincian potongan dari absensi --}}
                        <div id="rincianPotongan" class="d-none mt-2 p-2 rounded-2"
                             style="background:#fff0f0;font-size:.75rem;color:#c0392b;">
                            <div class="fw-600 mb-1">Rincian Potongan:</div>
                            <div>Terlambat: <span id="pot_terlambat">-</span></div>
                            <div>Alpha: <span id="pot_alpha">-</span></div>
                            <div>Izin: <span id="pot_izin">-</span></div>
                        </div>

                        <hr style="border-color:#eef0f4;margin:12px 0;">

                        <div class="p-3 rounded-3" style="background:linear-gradient(135deg,#edfaf4,#d5f5e3);">
                            <div style="font-size:.72rem;color:#27ae60;font-weight:600;
                                        text-transform:uppercase;letter-spacing:.5px;">
                                Total Gaji Diterima
                            </div>
                            <div id="prev_total" style="font-size:1.4rem;font-weight:700;color:#1a7a45;margin-top:4px;">
                                —
                            </div>
                        </div>

                        <div class="mt-3 p-2 rounded-2" style="background:#f8f9fc;font-size:.72rem;color:#8a9bb0;">
                            <i class="bi bi-info-circle me-1"></i>
                            Rumus: (Hari Kerja / 22 x Gaji Pokok) + Lembur + Bonus - Potongan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let calcTimeout = null;
let rekapTimeout = null;

// Load rekap absensi dari server
function loadRekapAbsensi() {
    const employeeId = document.getElementById('employee_id').value;
    const bulan      = document.getElementById('bulan_absensi').value;
    const tahun      = document.getElementById('tahun_absensi').value;

    if (!employeeId) return;

    clearTimeout(rekapTimeout);
    document.getElementById('rekapSpinner').classList.remove('d-none');

    rekapTimeout = setTimeout(() => {
        const data = new FormData();
        data.append('employee_id', employeeId);
        data.append('bulan', bulan);
        data.append('tahun', tahun);

        fetch('{{ route("absensi.rekap") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: data
        })
        .then(r => r.json())
        .then(result => {
            const rekap = result.rekap;

            if (rekap.hari_hadir === 0 && rekap.hari_alpha === 0 &&
                rekap.hari_izin === 0 && rekap.hari_sakit === 0) {
                document.getElementById('rekapAbsensiInfo').classList.add('d-none');
                document.getElementById('noAbsensiWarning').classList.remove('d-none');
            } else {
                document.getElementById('rekapAbsensiInfo').classList.remove('d-none');
                document.getElementById('noAbsensiWarning').classList.add('d-none');

                document.getElementById('rek_hadir').textContent    = rekap.hari_hadir + ' hari';
                document.getElementById('rek_alpha').textContent    = rekap.hari_alpha + ' hari';
                document.getElementById('rek_terlambat').textContent = rekap.total_terlambat + ' mnt';
                document.getElementById('rek_lembur').textContent   = rekap.total_lembur + ' jam';

                // Isi otomatis ke form
                document.getElementById('work_days').value      = rekap.hari_hadir;
                document.getElementById('overtime_hours').value = Math.floor(rekap.total_lembur);
                document.getElementById('deduction').value      = Math.round(rekap.total_potongan);
            }

            triggerCalc();
        })
        .finally(() => {
            document.getElementById('rekapSpinner').classList.add('d-none');
        });
    }, 300);
}

// Hitung preview gaji
function triggerCalc() {
    const employeeId = document.getElementById('employee_id').value;
    if (!employeeId) return;

    clearTimeout(calcTimeout);
    document.getElementById('calcSpinner').classList.remove('d-none');

    calcTimeout = setTimeout(() => {
        const data = new FormData();
        data.append('employee_id',    employeeId);
        data.append('work_days',      document.getElementById('work_days').value || 0);
        data.append('overtime_hours', document.getElementById('overtime_hours').value || 0);
        data.append('bonus',          document.getElementById('bonus').value || 0);
        data.append('deduction',      document.getElementById('deduction').value || 0);

        fetch('{{ route("penggajian.preview") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: data
        })
        .then(r => r.json())
        .then(result => {
            document.getElementById('previewEmpty').classList.add('d-none');
            document.getElementById('previewContent').classList.remove('d-none');

            document.getElementById('prev_gaji_pokok').textContent  = result.formatted.gaji_pokok;
            document.getElementById('prev_gaji_kerja').textContent  = result.formatted.gaji_kerja;
            document.getElementById('prev_hari_label').textContent  = `(${result.work_days ?? document.getElementById('work_days').value} hari)`;
            document.getElementById('prev_gaji_lembur').textContent = result.formatted.gaji_lembur;
            document.getElementById('prev_bonus').textContent       = '+ ' + result.formatted.bonus;
            document.getElementById('prev_deduction').textContent   = '- ' + result.formatted.deduction;
            document.getElementById('prev_total').textContent       = result.formatted.total_salary;

            // Tampilkan rincian potongan jika ada rekap
            if (result.rekap_absensi) {
                const r = result.rekap_absensi;
                document.getElementById('rincianPotongan').classList.remove('d-none');
                document.getElementById('pot_terlambat').textContent = result.formatted.deduction; // simplified
                document.getElementById('pot_alpha').textContent     = 'Rp ' + Number(r.potongan_alpha).toLocaleString('id-ID');
                document.getElementById('pot_izin').textContent      = 'Rp ' + Number(r.potongan_izin).toLocaleString('id-ID');
            }
        })
        .finally(() => {
            document.getElementById('calcSpinner').classList.add('d-none');
        });
    }, 400);
}

// Event listeners
document.getElementById('employee_id').addEventListener('change', () => {
    loadRekapAbsensi();
});
document.getElementById('bulan_absensi').addEventListener('change', loadRekapAbsensi);
document.getElementById('tahun_absensi').addEventListener('change', loadRekapAbsensi);

document.querySelectorAll('.calc-trigger').forEach(input => {
    input.addEventListener('input', triggerCalc);
});

// Auto-trigger jika karyawan sudah dipilih
window.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('employee_id').value) loadRekapAbsensi();
});
</script>
@endsection
