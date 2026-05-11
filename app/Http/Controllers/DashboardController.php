<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Role;
use App\Models\Payroll;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('dashboard', [
            'totalKaryawan'    => Employee::count(),
            'totalDepartemen'  => Department::count(),
            'totalJabatan'     => Role::count(),
            'totalPenggajian'  => Payroll::count(),
            'karyawanTerbaru'  => Employee::with(['role.department'])->latest()->take(5)->get(),
            'penggajianTerbaru' => Payroll::with(['employee.role'])->latest()->take(5)->get(),
        ]);
    }
}
