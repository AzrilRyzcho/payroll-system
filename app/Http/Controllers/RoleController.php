<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $jabatan = Role::with('department')->withCount('employees')->latest()->paginate(10);
        return view('jabatan.index', compact('jabatan'));
    }

    public function create()
    {
        $departemen = Department::orderBy('name')->get();
        return view('jabatan.create', compact('departemen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'salary'        => 'required|numeric|min:0',
        ]);

        Role::create($request->only('name', 'department_id', 'salary'));

        return redirect()->route('jabatan.index')
            ->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function show(Role $jabatan)
    {
        $jabatan->load(['department', 'employees']);
        return view('jabatan.show', ['item' => $jabatan]);
    }

    public function edit(Role $jabatan)
    {
        $departemen = Department::orderBy('name')->get();
        return view('jabatan.edit', ['item' => $jabatan, 'departemen' => $departemen]);
    }

    public function update(Request $request, Role $jabatan)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'salary'        => 'required|numeric|min:0',
        ]);

        $jabatan->update($request->only('name', 'department_id', 'salary'));

        return redirect()->route('jabatan.index')
            ->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Role $jabatan)
    {
        $jabatan->delete();

        return redirect()->route('jabatan.index')
            ->with('success', 'Jabatan berhasil dihapus.');
    }
}
