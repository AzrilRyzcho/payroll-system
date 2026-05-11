<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Gaji Karyawan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #fff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .top-nav {
            position: absolute;
            top: 20px;
            right: 30px;
        }
        .top-nav a {
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.75rem;
            color: #aaa;
            text-decoration: none;
            margin-left: 20px;
        }
        .top-nav a:hover {
            color: #555;
        }
        .hero {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero h1 {
            font-size: 3.5rem;
            font-weight: 200;
            color: #ccc;
            letter-spacing: 4px;
        }
    </style>
</head>
<body>
    <div class="top-nav">
        <a href="{{ route('login') }}">Masuk</a>
        <a href="{{ route('register') }}">Daftar</a>
    </div>
    <div class="hero">
        <h1>Sistem Gaji Karyawan</h1>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
