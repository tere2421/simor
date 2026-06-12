<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMOR — Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:    #0a1628;
            --navy2:   #112240;
            --accent:  #f97316;
            --accent2: #fb923c;
            --gold:    #f59e0b;
            --white:   #ffffff;
            --gray100: #f1f5f9;
            --gray400: #94a3b8;
            --gray600: #475569;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: var(--navy);
        }

        /* ── BACKGROUND ── */
        .bg-wrap {
            position: fixed; inset: 0; z-index: 0;
        }
        .bg-img {
            position: absolute; inset: 0;
            background:
                url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1800&q=80')
                center/cover no-repeat;
            filter: blur(6px) brightness(.35);
            transform: scale(1.08);
        }
        /* gradient overlay */
        .bg-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(
                135deg,
                rgba(10,22,40,.92) 0%,
                rgba(17,34,64,.80) 50%,
                rgba(10,22,40,.92) 100%
            );
        }
        /* noise texture */
        .bg-noise {
            position: absolute; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='4'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.04'/%3E%3C/svg%3E");
            opacity: .6;
        }

        /* ── FLOATING SHAPES ── */
        .shape {
            position: absolute; border-radius: 50%;
            filter: blur(60px); opacity: .18; z-index: 1;
            animation: float 8s ease-in-out infinite;
        }
        .shape-1 { width: 400px; height: 400px; background: var(--accent);  top: -10%;  left: -8%;  animation-delay: 0s; }
        .shape-2 { width: 300px; height: 300px; background: #3b82f6;        bottom: -5%; right: -5%; animation-delay: -3s; }
        .shape-3 { width: 200px; height: 200px; background: var(--gold);    top: 60%;   left: 10%;  animation-delay: -6s; }

        @keyframes float {
            0%,100% { transform: translateY(0) scale(1); }
            50%      { transform: translateY(-30px) scale(1.05); }
        }

        /* ── CARD ── */
        .card-wrap {
            position: relative; z-index: 10;
            display: flex; align-items: stretch;
            width: 900px; max-width: calc(100vw - 2rem);
            min-height: 520px;
            border-radius: 20px; overflow: hidden;
            box-shadow: 0 40px 100px rgba(0,0,0,.6), 0 0 0 1px rgba(255,255,255,.06);
            animation: cardIn .7s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes cardIn {
            from { opacity:0; transform: translateY(30px) scale(.97); }
            to   { opacity:1; transform: translateY(0) scale(1); }
        }

        /* ── LEFT PANEL ── */
        .panel-left {
            flex: 1; padding: 3rem;
            background: linear-gradient(160deg, #0d1f3c 0%, #0a1628 100%);
            border-right: 1px solid rgba(255,255,255,.06);
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .brand-logo {
            display: flex; align-items: center; gap: .75rem; margin-bottom: 2.5rem;
        }
        .brand-icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: linear-gradient(135deg, var(--accent), var(--gold));
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }
        .brand-name {
            font-family: 'DM Serif Display', serif;
            font-size: 1.6rem; color: var(--white); letter-spacing: .5px;
        }
        .brand-sub { font-size: .7rem; color: var(--gray400); margin-top: 1px; }

        .panel-tagline {
            flex: 1; display: flex; flex-direction: column; justify-content: center;
        }
        .tagline-label {
            font-size: .7rem; font-weight: 700; letter-spacing: 2px;
            text-transform: uppercase; color: var(--accent); margin-bottom: 1rem;
        }
        .tagline-title {
            font-family: 'DM Serif Display', serif;
            font-size: 2rem; line-height: 1.25; color: var(--white);
            margin-bottom: 1.25rem;
        }
        .tagline-title em { color: var(--accent2); font-style: italic; }
        .tagline-desc { font-size: .85rem; color: var(--gray400); line-height: 1.6; }

        .feature-list { margin-top: 2rem; display: flex; flex-direction: column; gap: .65rem; }
        .feature-item {
            display: flex; align-items: center; gap: .65rem;
            font-size: .8rem; color: rgba(255,255,255,.5);
        }
        .feature-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--accent); flex-shrink: 0;
        }

        /* ── RIGHT PANEL ── */
        .panel-right {
            width: 380px; padding: 3rem;
            background: rgba(255,255,255,.03);
            backdrop-filter: blur(20px);
            display: flex; flex-direction: column; justify-content: center;
        }
        .form-heading {
            margin-bottom: 2rem;
        }
        .form-heading h2 {
            font-size: 1.5rem; font-weight: 800; color: var(--white);
            margin-bottom: .35rem;
        }
        .form-heading p { font-size: .82rem; color: var(--gray400); }

        /* ── FORM ELEMENTS ── */
        .form-group { margin-bottom: 1.25rem; }
        .form-label {
            display: block; font-size: .78rem; font-weight: 600;
            color: var(--gray400); margin-bottom: .5rem; letter-spacing: .3px;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: .9rem; top: 50%; transform: translateY(-50%);
            color: var(--gray600); font-size: .85rem; pointer-events: none;
        }
        .form-input {
            width: 100%; padding: .75rem 1rem .75rem 2.5rem;
            background: rgba(255,255,255,.07);
            border: 1.5px solid rgba(255,255,255,.1);
            border-radius: 10px; color: var(--white); font-size: .9rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: border-color .2s, background .2s, box-shadow .2s;
            outline: none;
        }
        .form-input::placeholder { color: rgba(255,255,255,.2); }
        .form-input:focus {
            border-color: var(--accent);
            background: rgba(249,115,22,.07);
            box-shadow: 0 0 0 3px rgba(249,115,22,.12);
        }
        .form-input.error { border-color: #ef4444; }

        .toggle-pw {
            position: absolute; right: .9rem; top: 50%; transform: translateY(-50%);
            color: var(--gray600); cursor: pointer; font-size: .85rem;
            background: none; border: none; padding: 0;
            transition: color .15s;
        }
        .toggle-pw:hover { color: var(--white); }

        .error-msg { color: #f87171; font-size: .75rem; margin-top: .4rem; }

        /* ── REMEMBER + FORGOT ── */
        .row-remember {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .remember-label {
            display: flex; align-items: center; gap: .5rem;
            font-size: .8rem; color: var(--gray400); cursor: pointer;
        }
        .remember-label input[type=checkbox] {
            width: 15px; height: 15px; accent-color: var(--accent); cursor: pointer;
        }
        .forgot-link {
            font-size: .78rem; color: var(--accent); text-decoration: none;
            transition: color .15s;
        }
        .forgot-link:hover { color: var(--accent2); }

        /* ── SUBMIT BUTTON ── */
        .btn-login {
            width: 100%; padding: .85rem;
            background: linear-gradient(135deg, var(--accent), var(--gold));
            border: none; border-radius: 10px;
            color: var(--white); font-size: .95rem; font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer; letter-spacing: .3px;
            transition: transform .15s, box-shadow .15s, filter .15s;
            box-shadow: 0 4px 20px rgba(249,115,22,.35);
            display: flex; align-items: center; justify-content: center; gap: .6rem;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(249,115,22,.45);
            filter: brightness(1.05);
        }
        .btn-login:active { transform: translateY(0); }

        /* ── FOOTER ── */
        .form-footer {
            margin-top: 1.75rem; padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,.07);
            display: flex; align-items: center; gap: .6rem;
        }
        .footer-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--accent); flex-shrink: 0;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%,100% { opacity:1; transform:scale(1); }
            50%      { opacity:.5; transform:scale(.8); }
        }
        .footer-text { font-size: .74rem; color: var(--gray600); }
        .footer-text strong { color: var(--gray400); }

        @media (max-width: 680px) {
            .panel-left { display: none; }
            .card-wrap { width: 100%; min-height: 100vh; border-radius: 0; }
            .panel-right { width: 100%; padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>

<!-- Background -->
<div class="bg-wrap">
    <div class="bg-img"></div>
    <div class="bg-overlay"></div>
    <div class="bg-noise"></div>
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
</div>

<!-- Card -->
<div class="card-wrap">

    <!-- Left Panel -->
    <div class="panel-left">
        <div>
            <div class="brand-logo">
                <div class="brand-icon">🍽</div>
                <div>
                    <div class="brand-name">SIMOR</div>
                    <div class="brand-sub">Hangry Indonesia</div>
                </div>
            </div>

            <div class="panel-tagline">
                <div class="tagline-label">Sistem Operasional Terpusat</div>
                <h1 class="tagline-title">
                    Kelola restoran lebih<br>
                    <em>cepat & terstruktur</em>
                </h1>
                <p class="tagline-desc">
                    Platform manajemen operasional terintegrasi untuk stok, monitoring kualitas, dan penjadwalan staff Hangry Indonesia.
                </p>

                <div class="feature-list">
                    <div class="feature-item"><div class="feature-dot"></div> Monitoring stok real-time dengan alert otomatis</div>
                    <div class="feature-item"><div class="feature-dot"></div> Pencatatan suhu & checklist kebersihan per shift</div>
                    <div class="feature-item"><div class="feature-dot"></div> Grid jadwal mingguan dengan ekspor Excel / PDF</div>
                    <div class="feature-item"><div class="feature-dot"></div> Dashboard ringkasan operasional harian</div>
                </div>
            </div>
        </div>

        <div style="font-size:.72rem;color:rgba(255,255,255,.2);">
            © 2025 Hangry Indonesia · SIMOR v1.0
        </div>
    </div>

    <!-- Right Panel -->
    <div class="panel-right">
        <div class="form-heading">
            <h2>Selamat Datang</h2>
            <p>Masuk ke akun SIMOR kamu</p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div style="background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:#86efac;padding:.75rem 1rem;border-radius:8px;font-size:.82rem;margin-bottom:1.25rem;">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-envelope input-icon"></i>
                    <input id="email" type="email" name="email" class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                           value="{{ old('email') }}" placeholder="email@hangry.id" required autofocus autocomplete="email">
                </div>
                @error('email')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input id="password" type="password" name="password" class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                           placeholder="••••••••" required autocomplete="current-password">
                    <button type="button" class="toggle-pw" onclick="togglePw()" id="pwToggle">
                        <i class="fa-solid fa-eye" id="pwIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember + Forgot -->
            <div class="row-remember">
                <label class="remember-label">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Ingat saya
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
                @endif
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-login">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                Masuk ke SIMOR
            </button>
        </form>

        <div class="form-footer">
            <div class="footer-dot"></div>
            <div class="footer-text">
                Login sebagai <strong>SM · PIC · Staff</strong> — akses fitur sesuai role
            </div>
        </div>
    </div>

</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>
function togglePw() {
    const inp  = document.getElementById('password');
    const icon = document.getElementById('pwIcon');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'fa-solid fa-eye-slash';
    } else {
        inp.type = 'password';
        icon.className = 'fa-solid fa-eye';
    }
}
</script>
</body>
</html>
