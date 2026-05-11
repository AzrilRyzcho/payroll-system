<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'employee_id',
        'work_days',
        'overtime_hours',
        'bonus',
        'deduction',
        'total_salary',
        'payroll_date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Hitung total gaji:
     * (hari_kerja / 22) * gaji_pokok + (jam_lembur * tarif_lembur) + bonus - potongan
     * Tarif lembur = gaji_pokok / 22 / 8 * 1.5
     */
    public static function hitungTotalGaji(Employee $employee, int $workDays, int $overtimeHours, float $bonus, float $deduction): float
    {
        $gajiPokok = $employee->role->salary ?? 0;
        $gajiHarian = $gajiPokok / 22;
        $gajiKerja  = $gajiHarian * $workDays;
        $tarifLembur = ($gajiPokok / 22 / 8) * 1.5;
        $gajiLembur  = $tarifLembur * $overtimeHours;

        return max(0, $gajiKerja + $gajiLembur + $bonus - $deduction);
    }
}
