<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Gaji Karyawan')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }

        body {
            background: #f5f6fa;
            min-height: 100vh;
        }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: 240px;
            height: 100vh;
            background: #1e2a3a;
            z-index: 100;
            display: flex;
            flex-direction: column;
            transition: width .25s ease;
            overflow: hidden;
        }

        .sidebar-brand {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }
        .sidebar-brand .brand-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }
        .sidebar-brand .brand-sub {
            font-size: .7rem;
            color: #8a9bb0;
            letter-spacing: .5px;
            text-transform: uppercase;
        }
        .sidebar-brand .brand-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #4f8ef7, #7c5cfc);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; color: #fff;
            margin-bottom: 10px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }
        .sidebar-nav .nav-label {
            font-size: .65rem;
            font-weight: 600;
            color: #5a6a7e;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 8px 8px 4px;
            margin-top: 8px;
        }
        .sidebar-nav .nav-item { margin-bottom: 2px; }
        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            color: #8a9bb0;
            font-size: .875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all .2s;
        }
        .sidebar-nav .nav-link i {
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }
        .sidebar-nav .nav-link:hover {
            background: rgba(255,255,255,.06);
            color: #fff;
        }
        .sidebar-nav .nav-link.active {
            background: linear-gradient(135deg, rgba(79,142,247,.25), rgba(124,92,252,.25));
            color: #fff;
            border-left: 3px solid #4f8ef7;
        }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,.07);
        }
        .sidebar-footer .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px;
            border-radius: 8px;
        }
        .sidebar-footer .avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, #4f8ef7, #7c5cfc);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem; font-weight: 700; color: #fff;
            flex-shrink: 0;
        }
        .sidebar-footer .user-name {
            font-size: .8rem;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-footer .user-role {
            font-size: .7rem;
            color: #5a6a7e;
        }
        .sidebar-footer .logout-btn {
            background: none;
            border: none;
            color: #5a6a7e;
            font-size: .85rem;
            padding: 4px;
            cursor: pointer;
            transition: color .2s;
            margin-left: auto;
        }
        .sidebar-footer .logout-btn:hover { color: #e74c3c; }

        /* ── Main content ── */
        .main-wrapper {
            margin-left: 240px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Topbar ── */
        .topbar {
            background: #fff;
            border-bottom: 1px solid #eef0f4;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .topbar .page-heading {
            font-size: 1rem;
            font-weight: 600;
            color: #1e2a3a;
            margin: 0;
        }
        .topbar .breadcrumb {
            font-size: .75rem;
            margin: 0;
        }
        .topbar .breadcrumb-item a {
            color: #4f8ef7;
            text-decoration: none;
        }
        .topbar .breadcrumb-item.active { color: #8a9bb0; }
        .topbar .breadcrumb-item + .breadcrumb-item::before { color: #c5cdd8; }

        /* ── Content area ── */
        .content-area {
            padding: 28px;
            flex: 1;
        }

        /* ── Stat cards ── */
        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 22px 24px;
            border: 1px solid #eef0f4;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,.07);
        }
        .stat-card .stat-icon {
            width: 52px; height: 52px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        .stat-card .stat-label {
            font-size: .75rem;
            font-weight: 500;
            color: #8a9bb0;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 4px;
        }
        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e2a3a;
            line-height: 1;
        }
        .stat-card .stat-desc {
            font-size: .72rem;
            color: #b0bac8;
            margin-top: 4px;
        }

        .icon-blue   { background: #eff5ff; color: #4f8ef7; }
        .icon-purple { background: #f3f0ff; color: #7c5cfc; }
        .icon-green  { background: #edfaf4; color: #27ae60; }
        .icon-orange { background: #fff5ec; color: #e67e22; }

        /* ── Table card ── */
        .table-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #eef0f4;
            overflow: hidden;
        }
        .table-card .table-card-header {
            padding: 18px 24px;
            border-bottom: 1px solid #eef0f4;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .table-card .table-card-title {
            font-size: .9rem;
            font-weight: 600;
            color: #1e2a3a;
            margin: 0;
        }
        .table-card .table-card-body { padding: 0; }
        .table-card .table {
            margin: 0;
            font-size: .82rem;
        }
        .table-card .table thead th {
            background: #f8f9fc;
            color: #8a9bb0;
            font-weight: 600;
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            border-bottom: 1px solid #eef0f4;
            padding: 12px 20px;
        }
        .table-card .table tbody td {
            padding: 13px 20px;
            color: #3d4f63;
            border-bottom: 1px solid #f5f6fa;
            vertical-align: middle;
        }
        .table-card .table tbody tr:last-child td { border-bottom: none; }
        .table-card .table tbody tr:hover td { background: #fafbfd; }
        .table-card .table td a {
            color: #4f8ef7;
            text-decoration: none;
            font-weight: 500;
        }
        .table-card .table td a:hover { text-decoration: underline; }

        /* ── Badges ── */
        .badge-soft-blue   { background: #eff5ff; color: #4f8ef7; font-weight: 500; }
        .badge-soft-green  { background: #edfaf4; color: #27ae60; font-weight: 500; }
        .badge-soft-orange { background: #fff5ec; color: #e67e22; font-weight: 500; }

        /* ── Buttons ── */
        .btn-primary {
            background: linear-gradient(135deg, #4f8ef7, #7c5cfc);
            border: none;
            font-weight: 500;
            font-size: .85rem;
            padding: 8px 18px;
            border-radius: 8px;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #3a7de6, #6b4be0);
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: #f0f2f5;
            border: none;
            color: #3d4f63;
            font-weight: 500;
            font-size: .85rem;
            padding: 8px 18px;
            border-radius: 8px;
        }
        .btn-secondary:hover { background: #e4e7ec; color: #1e2a3a; }
        .btn-danger {
            background: #fff0f0;
            border: none;
            color: #e74c3c;
            font-weight: 500;
            font-size: .85rem;
            padding: 8px 18px;
            border-radius: 8px;
        }
        .btn-danger:hover { background: #ffe0e0; color: #c0392b; }
        .btn-outline-secondary {
            border: 1px solid #dde1e8;
            color: #3d4f63;
            font-weight: 500;
            font-size: .85rem;
            padding: 8px 18px;
            border-radius: 8px;
        }
        .btn-outline-secondary:hover { background: #f0f2f5; }

        /* ── Form card ── */
        .form-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #eef0f4;
            padding: 28px;
        }
        .form-label {
            font-size: .82rem;
            font-weight: 600;
            color: #3d4f63;
            margin-bottom: 6px;
        }
        .form-control, .form-select {
            border: 1px solid #dde1e8;
            border-radius: 8px;
            font-size: .875rem;
            padding: 9px 14px;
            color: #1e2a3a;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #4f8ef7;
            box-shadow: 0 0 0 3px rgba(79,142,247,.12);
        }
        .form-text { font-size: .75rem; color: #8a9bb0; }

        /* ── Alert ── */
        .alert-success {
            background: #edfaf4;
            border: 1px solid #a8e6c5;
            color: #1a7a45;
            border-radius: 10px;
            font-size: .85rem;
        }
        .alert-danger {
            background: #fff0f0;
            border: 1px solid #f5c0c0;
            color: #c0392b;
            border-radius: 10px;
            font-size: .85rem;
        }

        /* ── Page title ── */
        .page-title-section {
            margin-bottom: 24px;
        }
        .page-title-section h4 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e2a3a;
            margin: 0;
        }
        .page-title-section p {
            font-size: .8rem;
            color: #8a9bb0;
            margin: 4px 0 0;
        }

        /* ── Pagination ── */
        .pagination .page-link {
            border: 1px solid #eef0f4;
            color: #4f8ef7;
            font-size: .82rem;
            padding: 6px 12px;
            border-radius: 6px !important;
            margin: 0 2px;
        }
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #4f8ef7, #7c5cfc);
            border-color: transparent;
        }

        /* ── Avatar initials ── */
        .avatar-sm {
            width: 30px; height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .7rem;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

@auth
<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-currency-dollar"></i></div>
        <div class="brand-title">SistemGaji</div>
        <div class="brand-sub">Payroll Management</div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Menu Utama</div>

        <div class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
        </div>

        <div class="nav-label">Data Master</div>

        <div class="nav-item">
            <a href="{{ route('departemen.index') }}" class="nav-link {{ request()->is('departemen*') ? 'active' : '' }}">
                <i class="bi bi-building"></i> Departemen
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('jabatan.index') }}" class="nav-link {{ request()->is('jabatan*') ? 'active' : '' }}">
                <i class="bi bi-briefcase"></i> Jabatan
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('karyawan.index') }}" class="nav-link {{ request()->is('karyawan*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Karyawan
            </a>
        </div>

        <div class="nav-label">Penggajian</div>

        <div class="nav-item">
            <a href="{{ route('absensi.index') }}" class="nav-link {{ request()->is('absensi*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> Absensi
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('penggajian.index') }}" class="nav-link {{ request()->is('penggajian*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> Penggajian
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div style="flex:1; min-width:0;">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">Administrator</div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn" title="Keluar">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- Main wrapper -->
<div class="main-wrapper">
    <!-- Topbar -->
    <div class="topbar">
        <div>
            <h6 class="page-heading">@yield('page-title', 'Dashboard')</h6>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-secondary border" style="font-size:.72rem;">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('d M Y') }}
            </span>
        </div>
    </div>

    <!-- Content -->
    <div class="content-area">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>
</div>

@else
<!-- Guest: no sidebar -->
@yield('content')
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
