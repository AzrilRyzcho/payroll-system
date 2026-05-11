<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Sistem Gaji</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body {
            background: #f5f6fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            border: 1px solid #eef0f4;
            box-shadow: 0 8px 32px rgba(0,0,0,.06);
        }
        .brand-icon {
            width: 48px; height: 48px;
            background: linear-gradient(135deg, #4f8ef7, #7c5cfc);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; color: #fff;
            margin-bottom: 16px;
        }
        .auth-title { font-size: 1.3rem; font-weight: 700; color: #1e2a3a; }
        .auth-sub   { font-size: .82rem; color: #8a9bb0; margin-top: 4px; }
        .form-label { font-size: .82rem; font-weight: 600; color: #3d4f63; }
        .form-control {
            border: 1px solid #dde1e8; border-radius: 8px;
            font-size: .875rem; padding: 10px 14px;
        }
        .form-control:focus {
            border-color: #4f8ef7;
            box-shadow: 0 0 0 3px rgba(79,142,247,.12);
        }
        .btn-login {
            background: linear-gradient(135deg, #4f8ef7, #7c5cfc);
            border: none; color: #fff; font-weight: 600;
            padding: 11px; border-radius: 8px; width: 100%;
            font-size: .9rem; cursor: pointer;
        }
        .btn-login:hover { opacity: .9; }
        .alert-danger {
            background: #fff0f0; border: 1px solid #f5c0c0;
            color: #c0392b; border-radius: 8px; font-size: .82rem; padding: 12px;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="brand-icon"><i class="bi bi-currency-dollar"></i></div>
        <div class="auth-title">Selamat Datang</div>
        <div class="auth-sub mb-4">Masuk ke Sistem Gaji Karyawan</div>

        @if($errors->any())
        <div class="alert-danger mb-3 rounded-3">
            @foreach($errors->all() as $error)
                <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
            @endforeach
        </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Alamat Email</label>
                <input type="email" name="email" class="form-control"
                       placeholder="email@contoh.com"
                       value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Kata Sandi</label>
                <input type="password" name="password" class="form-control"
                       placeholder="••••••••" required>
            </div>
            <div class="mb-4 d-flex align-items-center">
                <input type="checkbox" name="remember" class="form-check-input me-2" id="remember">
                <label for="remember" style="font-size:.82rem;color:#3d4f63;cursor:pointer;">Ingat Saya</label>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>

        <div class="text-center mt-4" style="font-size:.82rem;color:#8a9bb0;">
            Belum punya akun?
            <a href="{{ route('register') }}" style="color:#4f8ef7;font-weight:600;text-decoration:none;">Daftar sekarang</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
