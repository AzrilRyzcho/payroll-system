<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = Payroll::with('employee.role');

        if ($request->filled('karyawan_id')) {
            $query->where('employee_id', $request->karyawan_id);
        }

        $penggajian = $query->latest()->paginate(10);
        return view('penggajian.index', compact('penggajian'));
    }

    public function create(Request $request)
    {
        $karyawan = Employee::with('role')->orderBy('name')->get();
        return view('penggajian.create', compact('karyawan'));
    }

    /**
     * AJAX: Hitung preview gaji sebelum disimpan
     */
    public function preview(Request $request)
    {
        $request->validate([
            'employee_id'    => 'required|exists:employees,id',
            'work_days'      => 'required|integer|min:0|max:31',
            'overtime_hours' => 'nullable|integer|min:0',
            'bonus'          => 'nullable|numeric|min:0',
            'deduction'      => 'nullable|numeric|min:0',
        ]);

        $employee      = Employee::with('role')->findOrFail($request->employee_id);
        $overtimeHours = (int) ($request->overtime_hours ?? 0);
        $bonus         = (float) ($request->bonus ?? 0);
        $deduction     = (float) ($request->deduction ?? 0);
        $workDays      = (int) $request->work_days;

        $gajiPokok   = $employee->role->salary ?? 0;
        $gajiHarian  = $gajiPokok / 22;
        $gajiKerja   = $gajiHarian * $workDays;
        $tarifLembur = ($gajiPokok / 22 / 8) * 1.5;
        $gajiLembur  = $tarifLembur * $overtimeHours;
        $total       = max(0, $gajiKerja + $gajiLembur + $bonus - $deduction);

        return response()->json([
            'gaji_pokok'   => $gajiPokok,
            'gaji_kerja'   => round($gajiKerja),
            'gaji_lembur'  => round($gajiLembur),
            'bonus'        => $bonus,
            'deduction'    => $deduction,
            'total_salary' => round($total),
            'formatted'    => [
                'gaji_pokok'   => 'Rp ' . number_format($gajiPokok, 0, ',', '.'),
                'gaji_kerja'   => 'Rp ' . number_format($gajiKerja, 0, ',', '.'),
                'gaji_lembur'  => 'Rp ' . number_format($gajiLembur, 0, ',', '.'),
                'bonus'        => 'Rp ' . number_format($bonus, 0, ',', '.'),
                'deduction'    => 'Rp ' . number_format($deduction, 0, ',', '.'),
                'total_salary' => 'Rp ' . number_format($total, 0, ',', '.'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id'    => 'required|exists:employees,id',
            'payroll_date'   => 'required|date',
            'work_days'      => 'required|integer|min:0|max:31',
            'overtime_hours' => 'nullable|integer|min:0',
            'bonus'          => 'nullable|numeric|min:0',
            'deduction'      => 'nullable|numeric|min:0',
        ]);

        $employee      = Employee::findOrFail($request->employee_id);
        $overtimeHours = (int) ($request->overtime_hours ?? 0);
        $bonus         = (float) ($request->bonus ?? 0);
        $deduction     = (float) ($request->deduction ?? 0);

        $totalGaji = Payroll::hitungTotalGaji(
            $employee,
            (int) $request->work_days,
            $overtimeHours,
            $bonus,
            $deduction
        );

        Payroll::create([
            'employee_id'    => $request->employee_id,
            'payroll_date'   => $request->payroll_date,
            'work_days'      => $request->work_days,
            'overtime_hours' => $overtimeHours,
            'bonus'          => $bonus,
            'deduction'      => $deduction,
            'total_salary'   => $totalGaji,
        ]);

        return redirect()->route('penggajian.index')
            ->with('success', 'Data penggajian berhasil ditambahkan.');
    }

    public function show(Payroll $penggajian)
    {
        $penggajian->load('employee.role.department');
        return view('penggajian.show', ['item' => $penggajian]);
    }

    public function edit(Payroll $penggajian)
    {
        $karyawan = Employee::with('role')->orderBy('name')->get();
        return view('penggajian.edit', ['item' => $penggajian, 'karyawan' => $karyawan]);
    }

    public function update(Request $request, Payroll $penggajian)
    {
        $request->validate([
            'employee_id'    => 'required|exists:employees,id',
            'payroll_date'   => 'required|date',
            'work_days'      => 'required|integer|min:0|max:31',
            'overtime_hours' => 'nullable|integer|min:0',
            'bonus'          => 'nullable|numeric|min:0',
            'deduction'      => 'nullable|numeric|min:0',
        ]);

        $employee      = Employee::findOrFail($request->employee_id);
        $overtimeHours = (int) ($request->overtime_hours ?? 0);
        $bonus         = (float) ($request->bonus ?? 0);
        $deduction     = (float) ($request->deduction ?? 0);

        $totalGaji = Payroll::hitungTotalGaji(
            $employee,
            (int) $request->work_days,
            $overtimeHours,
            $bonus,
            $deduction
        );

        $penggajian->update([
            'employee_id'    => $request->employee_id,
            'payroll_date'   => $request->payroll_date,
            'work_days'      => $request->work_days,
            'overtime_hours' => $overtimeHours,
            'bonus'          => $bonus,
            'deduction'      => $deduction,
            'total_salary'   => $totalGaji,
        ]);

        return redirect()->route('penggajian.index')
            ->with('success', 'Data penggajian berhasil diperbarui.');
    }

    public function destroy(Payroll $penggajian)
    {
        $penggajian->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data penggajian berhasil dihapus.',
            ]);
        }

        return redirect()->route('penggajian.index')
            ->with('success', 'Data penggajian berhasil dihapus.');
    }
}
