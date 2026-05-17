<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\AbsensiController;

// Halaman utama
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login']);
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register']);
});

Route::post('/keluar', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Halaman yang membutuhkan login
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    // Departemen
    Route::resource('departemen', DepartmentController::class);

    // Jabatan
    Route::resource('jabatan', RoleController::class);

    // Karyawan
    Route::get('/karyawan/sampah', [EmployeeController::class, 'sampah'])->name('karyawan.sampah');
    Route::post('/karyawan/{id}/pulihkan', [EmployeeController::class, 'pulihkan'])->name('karyawan.pulihkan');
    Route::delete('/karyawan/{id}/hapus-permanent', [EmployeeController::class, 'hapusPermanent'])->name('karyawan.hapusPermanent');
    Route::resource('karyawan', EmployeeController::class);

    // Penggajian
    Route::post('/penggajian/preview', [PayrollController::class, 'preview'])->name('penggajian.preview');
    Route::resource('penggajian', PayrollController::class);

    // Absensi
    Route::get('/absensi/pengaturan', [AbsensiController::class, 'pengaturan'])->name('absensi.pengaturan');
    Route::post('/absensi/pengaturan', [AbsensiController::class, 'simpanPengaturan'])->name('absensi.simpanPengaturan');
    Route::post('/absensi/rekap', [AbsensiController::class, 'rekap'])->name('absensi.rekap');
    Route::resource('absensi', AbsensiController::class);
});
