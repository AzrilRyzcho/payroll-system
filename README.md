# 💰 Sistem Gaji Karyawan

Aplikasi web manajemen penggajian karyawan berbasis **Laravel** dengan tampilan modern menggunakan **Bootstrap 5** dan interaksi dinamis menggunakan **AJAX**.

---

## 🖥️ Tampilan Aplikasi

| Halaman | Deskripsi |
|---|---|
| Login / Daftar | Autentikasi pengguna |
| Dashboard | Statistik ringkasan + data terbaru |
| Departemen | CRUD via modal AJAX |
| Jabatan | CRUD dengan relasi departemen |
| Karyawan | CRUD via modal AJAX + soft delete |
| Penggajian | CRUD + preview kalkulasi gaji real-time |

---

## ⚙️ Teknologi yang Digunakan

| Teknologi | Versi | Keterangan |
|---|---|---|
| PHP | 8.3+ | Bahasa pemrograman utama |
| Laravel | 13.x | Framework PHP |
| MySQL | 5.7+ | Database |
| Bootstrap | 5.3 | Framework CSS |
| Bootstrap Icons | 1.11 | Ikon UI |
| AJAX (Fetch API) | — | Interaksi tanpa reload halaman |
| Google Fonts (Inter) | — | Tipografi |

---

## 📁 Struktur Fitur

```
payroll-system/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php          # Login, Register, Logout
│   │   ├── DashboardController.php     # Statistik dashboard
│   │   ├── DepartmentController.php    # CRUD Departemen (AJAX)
│   │   ├── RoleController.php          # CRUD Jabatan
│   │   ├── EmployeeController.php      # CRUD Karyawan (AJAX + Soft Delete)
│   │   └── PayrollController.php       # CRUD Penggajian + Preview AJAX
│   └── Models/
│       ├── Department.php              # Relasi: hasMany Role
│       ├── Role.php                    # Relasi: belongsTo Dept, hasMany Employee
│       ├── Employee.php                # Relasi: belongsTo Role, hasMany Payroll
│       └── Payroll.php                 # Relasi: belongsTo Employee + hitungTotalGaji()
├── database/
│   ├── migrations/                     # Skema tabel
│   └── seeders/                        # Data awal
├── resources/views/
│   ├── layouts/app.blade.php           # Layout utama (sidebar + topbar)
│   ├── auth/                           # Login & Register
│   ├── departemen/                     # Views departemen
│   ├── jabatan/                        # Views jabatan
│   ├── karyawan/                       # Views karyawan + tempat sampah
│   └── penggajian/                     # Views penggajian + preview
└── routes/web.php                      # Semua route aplikasi
```

---

## 🗄️ Struktur Database

```
departments
├── id, name, timestamps

roles
├── id, department_id (FK), name, salary, timestamps

employees
├── id, role_id (FK), name, email, phone, address, timestamps, deleted_at

payrolls
├── id, employee_id (FK), work_days, overtime_hours
├── bonus, deduction, total_salary, payroll_date, timestamps
```

**Relasi:**
```
Department → Role (1:N)
Role       → Employee (1:N)
Employee   → Payroll (1:N)
```

---

## 🧮 Rumus Kalkulasi Gaji

```
Total Gaji = (Hari Kerja ÷ 22 × Gaji Pokok)
           + (Jam Lembur × Tarif Lembur)
           + Bonus
           − Potongan

Tarif Lembur = (Gaji Pokok ÷ 22 ÷ 8) × 1.5
```

---

## ⚡ Fitur AJAX

| Fitur | Endpoint | Keterangan |
|---|---|---|
| Tambah Departemen | `POST /departemen` | Modal, tanpa reload |
| Ubah Departemen | `PUT /departemen/{id}` | Fetch data lalu modal |
| Hapus Departemen | `DELETE /departemen/{id}` | Hapus baris langsung |
| Tambah Karyawan | `POST /karyawan` | Modal form lengkap |
| Ubah Karyawan | `PUT /karyawan/{id}` | Fetch data lalu modal |
| Hapus Karyawan | `DELETE /karyawan/{id}` | Soft delete, hapus baris |
| Preview Gaji | `POST /penggajian/preview` | Kalkulasi real-time (debounce 400ms) |

---

## 🚀 Cara Instalasi

### Prasyarat
- PHP >= 8.3
- Composer
- MySQL
- XAMPP / Laragon / server lokal lainnya

### Langkah Instalasi

**1. Clone repository**
```bash
git clone https://github.com/AzrilRyzcho/payroll-system.git
cd payroll-system
```

**2. Install dependensi PHP**
```bash
composer install
```

**3. Salin file environment**
```bash
cp .env.example .env
```

**4. Generate application key**
```bash
php artisan key:generate
```

**5. Konfigurasi database**

Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=payroll_db
DB_USERNAME=root
DB_PASSWORD=
```

**6. Buat database**
```sql
CREATE DATABASE payroll_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**7. Jalankan migrasi**
```bash
php artisan migrate
```

**8. (Opsional) Buat akun admin via tinker**
```bash
php artisan tinker
>>> App\Models\User::create(['name'=>'Admin','email'=>'admin@admin.com','password'=>bcrypt('password123')]);
```

**9. Jalankan server**
```bash
php artisan serve
```

**10. Akses aplikasi**
```
http://127.0.0.1:8000
```

---

## 👤 Akun Default (setelah seeder)

| Field | Value |
|---|---|
| Email | `admin@admin.com` |
| Password | `password123` |

Atau daftar akun baru melalui halaman `/daftar`.

---

## 📋 Daftar Route Utama

| Method | URI | Nama | Keterangan |
|---|---|---|---|
| GET | `/` | `home` | Halaman utama |
| GET | `/masuk` | `login` | Form login |
| POST | `/masuk` | — | Proses login |
| GET | `/daftar` | `register` | Form daftar |
| POST | `/daftar` | — | Proses daftar |
| POST | `/keluar` | `logout` | Logout |
| GET | `/dashboard` | `dashboard` | Dashboard |
| Resource | `/departemen` | `departemen.*` | CRUD Departemen |
| Resource | `/jabatan` | `jabatan.*` | CRUD Jabatan |
| Resource | `/karyawan` | `karyawan.*` | CRUD Karyawan |
| GET | `/karyawan/sampah` | `karyawan.sampah` | Tempat sampah |
| POST | `/karyawan/{id}/pulihkan` | `karyawan.pulihkan` | Pulihkan karyawan |
| DELETE | `/karyawan/{id}/hapus-permanent` | `karyawan.hapusPermanent` | Hapus permanen |
| Resource | `/penggajian` | `penggajian.*` | CRUD Penggajian |
| POST | `/penggajian/preview` | `penggajian.preview` | Preview kalkulasi (AJAX) |

---

## 🔒 Keamanan

- Semua route dilindungi middleware `auth` kecuali halaman publik
- CSRF token pada setiap form dan request AJAX
- Validasi input di sisi server pada setiap controller
- Password di-hash menggunakan `bcrypt`
- Soft delete untuk data karyawan (tidak langsung dihapus permanen)

---

## 👨‍💻 Developer

| | |
|---|---|
| **Nama** | Azril Ryzcho |
| **Email** | isukirman196@gmail.com |
| **GitHub** | [@AzrilRyzcho](https://github.com/AzrilRyzcho) |

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan pembelajaran. Bebas digunakan dan dimodifikasi.
