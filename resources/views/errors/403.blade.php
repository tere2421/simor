<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMOR — Akses Ditolak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #f1f5f9; min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        .box {
            background: #fff; border-radius: 1rem;
            padding: 3rem 2.5rem; text-align: center;
            max-width: 440px; width: 100%;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
        }
        .icon-wrap {
            width: 80px; height: 80px; border-radius: 50%;
            background: #fef2f2; margin: 0 auto 1.5rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: #ef4444;
        }
        h1 { font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: .5rem; }
        p  { font-size: .9rem; color: #64748b; line-height: 1.6; margin-bottom: 1.5rem; }
        .role-info {
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: .5rem;
            padding: .875rem 1.25rem; margin-bottom: 1.5rem; text-align: left;
        }
        .role-info .label { font-size: .72rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; }
        .role-info .value { font-size: .9rem; font-weight: 700; color: #1e293b; margin-top: .2rem; }
        .role-chip {
            display: inline-block; padding: 2px 10px; border-radius: 4px;
            font-size: .75rem; font-weight: 700; margin-top: .25rem;
        }
        .role-SM    { background: #fbbf24; color: #78350f; }
        .role-PIC   { background: #60a5fa; color: #1e3a8a; }
        .role-Staff { background: #34d399; color: #064e3b; }
        .btn-back {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .65rem 1.5rem; background: #0f2035; color: #fff;
            border-radius: .5rem; font-size: .9rem; font-weight: 600;
            text-decoration: none; transition: background .15s;
        }
        .btn-back:hover { background: #1a3350; }
    </style>
</head>
<body>
<div class="box">
    <div class="icon-wrap">
        <i class="fa-solid fa-lock"></i>
    </div>
    <h1>Akses Ditolak</h1>
    <p>Halaman ini tidak tersedia untuk role kamu. Hubungi Store Manager jika kamu merasa ini adalah kesalahan.</p>

    @auth
    <div class="role-info">
        <div class="label">Login sebagai</div>
        <div class="value">{{ auth()->user()->name }}</div>
        <span class="role-chip role-{{ auth()->user()->role }}">{{ auth()->user()->role }}</span>
    </div>
    @endauth

    <a href="{{ route('dashboard') }}" class="btn-back">
        <i class="fa-solid fa-house"></i> Kembali ke Dashboard
    </a>
</div>
</body>
</html>
