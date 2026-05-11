<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $karyawan = Employee::with('role.department')->latest()->paginate(10);
        return view('karyawan.index', compact('karyawan'));
    }

    public function create()
    {
        $jabatan = Role::with('department')->orderBy('name')->get();
        return view('karyawan.create', compact('jabatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:employees,email',
            'role_id' => 'required|exists:roles,id',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $item = Employee::create($request->only('name', 'email', 'role_id', 'phone', 'address'));

        if ($request->ajax()) {
            $item->load('role.department');
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil ditambahkan.',
                'data'    => [
                    'id'         => $item->id,
                    'name'       => $item->name,
                    'email'      => $item->email,
                    'role'       => $item->role->name ?? '-',
                    'department' => $item->role->department->name ?? '-',
                    'created_at' => $item->created_at->format('d M Y'),
                ],
            ]);
        }

        return redirect()->route('karyawan.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show(Employee $karyawan)
    {
        $karyawan->load(['role.department', 'payrolls']);
        return view('karyawan.show', ['item' => $karyawan]);
    }

    public function edit(Employee $karyawan)
    {
        if (request()->ajax()) {
            $karyawan->load('role');
            return response()->json([
                'id'      => $karyawan->id,
                'name'    => $karyawan->name,
                'email'   => $karyawan->email,
                'phone'   => $karyawan->phone,
                'role_id' => $karyawan->role_id,
                'address' => $karyawan->address,
            ]);
        }

        $jabatan = Role::with('department')->orderBy('name')->get();
        return view('karyawan.edit', ['item' => $karyawan, 'jabatan' => $jabatan]);
    }

    public function update(Request $request, Employee $karyawan)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:employees,email,' . $karyawan->id,
            'role_id' => 'required|exists:roles,id',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $karyawan->update($request->only('name', 'email', 'role_id', 'phone', 'address'));

        if ($request->ajax()) {
            $karyawan->load('role.department');
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil diperbarui.',
                'data'    => [
                    'id'         => $karyawan->id,
                    'name'       => $karyawan->name,
                    'email'      => $karyawan->email,
                    'role'       => $karyawan->role->name ?? '-',
                    'department' => $karyawan->role->department->name ?? '-',
                ],
            ]);
        }

        return redirect()->route('karyawan.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $karyawan)
    {
        $karyawan->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dipindahkan ke tempat sampah.',
            ]);
        }

        return redirect()->route('karyawan.index')
            ->with('success', 'Karyawan berhasil dipindahkan ke tempat sampah.');
    }

    public function sampah()
    {
        $karyawan = Employee::onlyTrashed()->with('role')->latest()->get();
        return view('karyawan.sampah', compact('karyawan'));
    }

    public function pulihkan($id)
    {
        Employee::onlyTrashed()->findOrFail($id)->restore();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dipulihkan.',
            ]);
        }

        return redirect()->route('karyawan.sampah')
            ->with('success', 'Karyawan berhasil dipulihkan.');
    }

    public function hapusPermanent($id)
    {
        Employee::onlyTrashed()->findOrFail($id)->forceDelete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dihapus secara permanen.',
            ]);
        }

        return redirect()->route('karyawan.sampah')
            ->with('success', 'Karyawan berhasil dihapus secara permanen.');
    }
}
