<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departemen = Department::withCount('roles')->latest()->paginate(10);
        return view('departemen.index', compact('departemen'));
    }

    public function create()
    {
        return view('departemen.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $item = Department::create(['name' => $validated['name']]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Departemen berhasil ditambahkan.',
                'data'    => [
                    'id'          => $item->id,
                    'name'        => $item->name,
                    'roles_count' => 0,
                    'created_at'  => $item->created_at->format('d M Y'),
                ],
            ]);
        }

        return redirect()->route('departemen.index')
            ->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function show(Department $departeman)
    {
        $departeman->load('roles.employees');
        return view('departemen.show', ['item' => $departeman]);
    }

    public function edit(Department $departeman)
    {
        if (request()->ajax()) {
            return response()->json([
                'id'   => $departeman->id,
                'name' => $departeman->name,
            ]);
        }
        return view('departemen.edit', ['item' => $departeman]);
    }

    public function update(Request $request, Department $departeman)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $departeman->update(['name' => $validated['name']]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Departemen berhasil diperbarui.',
                'data'    => [
                    'id'   => $departeman->id,
                    'name' => $departeman->name,
                ],
            ]);
        }

        return redirect()->route('departemen.index')
            ->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Department $departeman)
    {
        $departeman->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Departemen berhasil dihapus.',
            ]);
        }

        return redirect()->route('departemen.index')
            ->with('success', 'Departemen berhasil dihapus.');
    }
}
