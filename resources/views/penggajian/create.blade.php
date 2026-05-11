@extends('layouts.app')
@section('title', 'Tambah Penggajian')
@section('page-title', 'Tambah Penggajian')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('penggajian.index') }}">Penggajian</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="page-title-section">
            <h4>Tambah Penggajian</h4>
            <p>Total gaji dihitung otomatis secara real-time saat Anda mengisi form</p>
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
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                                <select name="employee_id" id="employee_id" class="form-select" required>
                                    <option value="">-- Pilih Karyawan --</option>
                                    @foreach($karyawan as $k)
                                    <option value="{{ $k->id }}"
                                        data-salary="{{ $k->role->salary ?? 0 }}"
                                        {{ old('employee_id', request('karyawan_id')) == $k->id ? 'selected' : '' }}>
                                        {{ $k->name }} — {{ $k->role->name ?? '-' }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Penggajian <span class="text-danger">*</span></label>
                                <input type="date" name="payroll_date" class="form-control"
                                       value="{{ old('payroll_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hari Kerja <span class="text-danger">*</span></label>
                                <input type="number" name="work_days" id="work_days" class="form-control calc-trigger"
                                       value="{{ old('work_days', 22) }}" min="0" max="31" required>
                                <div class="form-text">Standar: 22 hari kerja per bulan</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jam Lembur</label>
                                <input type="number" name="overtime_hours" id="overtime_hours" class="form-control calc-trigger"
                                       value="{{ old('overtime_hours', 0) }}" min="0">
                                <div class="form-text">Tarif: 1.5× gaji per jam</div>
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
                <div class="form-card" id="previewCard">
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
                                <td class="text-muted py-1">Gaji Kerja</td>
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
                            Rumus: (Hari Kerja ÷ 22 × Gaji Pokok) + Lembur + Bonus − Potongan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let calcTimeout = null;

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
        .then(res => res.json())
        .then(result => {
            document.getElementById('previewEmpty').classList.add('d-none');
            document.getElementById('previewContent').classList.remove('d-none');

            document.getElementById('prev_gaji_pokok').textContent  = result.formatted.gaji_pokok;
            document.getElementById('prev_gaji_kerja').textContent  = result.formatted.gaji_kerja;
            document.getElementById('prev_gaji_lembur').textContent = result.formatted.gaji_lembur;
            document.getElementById('prev_bonus').textContent       = '+ ' + result.formatted.bonus;
            document.getElementById('prev_deduction').textContent   = '− ' + result.formatted.deduction;
            document.getElementById('prev_total').textContent       = result.formatted.total_salary;
        })
        .catch(() => {})
        .finally(() => {
            document.getElementById('calcSpinner').classList.add('d-none');
        });
    }, 400); // debounce 400ms
}

// Trigger saat karyawan dipilih
document.getElementById('employee_id').addEventListener('change', triggerCalc);

// Trigger saat input angka berubah
document.querySelectorAll('.calc-trigger').forEach(input => {
    input.addEventListener('input', triggerCalc);
});

// Auto-trigger jika karyawan sudah dipilih (dari query string)
window.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('employee_id').value) triggerCalc();
});
</script>
@endsection
