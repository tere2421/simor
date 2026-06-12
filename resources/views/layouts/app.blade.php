<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMOR — @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --sidebar-w: 260px;
            --navy: #0f2035;
            --navy-light: #1a3350;
            --accent: #3b82f6;
            --accent-hover: #2563eb;
        }
        body { font-family: 'Inter', system-ui, sans-serif; background: #f1f5f9; }

        /* ── SIDEBAR ── */
        #sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w); background: var(--navy);
            display: flex; flex-direction: column; z-index: 100;
            transition: transform .25s ease;
        }
        .sidebar-logo { padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,.08); }
        .logo-text    { font-size: 1.5rem; font-weight: 800; color: #fff; }
        .logo-sub     { font-size: .68rem; color: #94a3b8; margin-top: 2px; }

        .nav-section-label {
            font-size: .65rem; font-weight: 700; letter-spacing: 1.2px;
            color: #64748b; text-transform: uppercase;
            padding: .75rem 1.5rem .3rem;
        }
        .nav-link {
            display: flex; align-items: center; gap: .75rem;
            padding: .6rem 1.5rem; color: #94a3b8; font-size: .875rem;
            text-decoration: none; transition: all .15s;
        }
        .nav-link:hover  { color: #fff; background: var(--navy-light); }
        .nav-link.active { color: #fff; background: var(--accent); font-weight: 600; }
        .nav-link .icon  { width: 18px; text-align: center; flex-shrink: 0; }

        .role-chip {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 8px; border-radius: 4px; font-size: .65rem; font-weight: 700;
        }
        .role-SM    { background: #fbbf24; color: #78350f; }
        .role-PIC   { background: #60a5fa; color: #1e3a8a; }
        .role-Staff { background: #34d399; color: #064e3b; }

        .sidebar-footer {
            margin-top: auto; padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .user-chip   { display: flex; align-items: center; gap: .75rem; }
        .user-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--accent); display: flex; align-items: center;
            justify-content: center; font-weight: 700; color: #fff;
            font-size: .85rem; flex-shrink: 0;
        }
        .user-name { color: #fff; font-size: .85rem; font-weight: 600; }

        /* ── MAIN ── */
        #main    { margin-left: var(--sidebar-w); min-height: 100vh; }
        .topbar  {
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: .85rem 1.75rem; display: flex; align-items: center;
            justify-content: space-between; position: sticky; top: 0; z-index: 50;
        }
        .topbar h1 { font-size: 1.1rem; font-weight: 700; color: #1e293b; }
        .topbar .breadcrumb { font-size: .78rem; color: #94a3b8; margin-top: 1px; }
        .content { padding: 1.75rem; }

        /* ── ALERT ── */
        .alert { border-radius: .5rem; padding: .875rem 1.25rem; margin-bottom: 1.25rem;
                 display: flex; align-items: center; gap: .75rem; font-size: .875rem; }
        .alert-success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
        .alert-error   { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; }
        .alert-warning { background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; }

        /* ── CARD ── */
        .card { background: #fff; border-radius: .75rem; box-shadow: 0 1px 3px rgba(0,0,0,.08); overflow: hidden; }
        .card-header { padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
        .card-header h3 { font-size: .95rem; font-weight: 700; color: #1e293b; }
        .card-body  { padding: 1.25rem; }

        /* ── STAT CARD ── */
        .stat-card {
            background: #fff; border-radius: .75rem; padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.07);
            display: flex; align-items: center; gap: 1rem;
            border-left: 4px solid transparent;
        }
        .stat-icon { width: 52px; height: 52px; border-radius: .625rem; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
        .stat-value { font-size: 1.75rem; font-weight: 800; color: #1e293b; line-height: 1; }
        .stat-label { font-size: .8rem; color: #64748b; margin-top: 4px; }

        /* ── TABLE ── */
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; color: #64748b; font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; padding: .75rem 1rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
        td { padding: .75rem 1rem; border-bottom: 1px solid #f1f5f9; font-size: .875rem; color: #334155; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8fafc; }

        /* ── BADGE ── */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 10px; border-radius: 9999px; font-size: .72rem; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-info    { background: #dbeafe; color: #1e40af; }
        .badge-gray    { background: #f1f5f9; color: #475569; }

        /* ── FORM ── */
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: .83rem; font-weight: 600; color: #374151; margin-bottom: .4rem; }
        .form-control { width: 100%; padding: .6rem .9rem; border: 1px solid #d1d5db; border-radius: .5rem; font-size: .9rem; color: #1e293b; transition: border-color .15s, box-shadow .15s; background: #fff; outline: none; }
        .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
        .form-control.is-invalid { border-color: #ef4444; }
        .invalid-feedback { color: #ef4444; font-size: .78rem; margin-top: .25rem; }

        /* ── BUTTON ── */
        .btn { display: inline-flex; align-items: center; gap: .5rem; padding: .55rem 1.1rem; border-radius: .5rem; font-size: .875rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all .15s; }
        .btn-primary   { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent-hover); }
        .btn-success   { background: #22c55e; color: #fff; }
        .btn-success:hover { background: #16a34a; }
        .btn-danger    { background: #ef4444; color: #fff; }
        .btn-danger:hover { background: #dc2626; }
        .btn-secondary { background: #f1f5f9; color: #475569; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-warning   { background: #f59e0b; color: #fff; }
        .btn-warning:hover { background: #d97706; }
        .btn-sm { padding: .35rem .75rem; font-size: .78rem; }

        /* ── PAGINATION ── */
        .pagination { display: flex; gap: .35rem; align-items: center; margin-top: 1rem; }
        .pagination a, .pagination span { padding: .4rem .75rem; border-radius: .4rem; font-size: .8rem; font-weight: 600; text-decoration: none; color: #475569; background: #f1f5f9; border: 1px solid #e2e8f0; }
        .pagination a:hover { background: var(--accent); color: #fff; border-color: var(--accent); }
        .pagination .active { background: var(--accent); color: #fff; border-color: var(--accent); }

        /* ── MISC ── */
        .divider { height: 1px; background: rgba(255,255,255,.06); margin: .5rem 1.5rem; }
        .grid { display: grid; }
        .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-cols-4 { grid-template-columns: repeat(4, 1fr); }
        .gap-4 { gap: 1rem; } .gap-6 { gap: 1.5rem; }
        .mb-4 { margin-bottom: 1rem; } .mb-6 { margin-bottom: 1.5rem; }
        .mt-4 { margin-top: 1rem; }
        .flex { display: flex; } .items-center { align-items: center; } .justify-between { justify-content: space-between; }
        .gap-2 { gap: .5rem; }
        .text-sm { font-size: .875rem; } .text-xs { font-size: .75rem; }
        .text-muted { color: #94a3b8; } .font-bold { font-weight: 700; } .font-semibold { font-weight: 600; }
        .text-right { text-align: right; } .text-center { text-align: center; }
        .w-full { width: 100%; } .rounded { border-radius: .5rem; } .p-4 { padding: 1rem; }
    </style>
    @stack('styles')
</head>
<body>

{{-- ═══ SIDEBAR ═══ --}}
<nav id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-text">🍽 SIMOR</div>
        <div class="logo-sub">Hangry Indonesia</div>
    </div>

    {{-- ══ NAVIGASI ══ --}}
    <div style="overflow-y:auto;flex:1;padding-bottom:1rem;">

        {{-- Dashboard --}}
        <div class="nav-section-label">Utama</div>
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high icon"></i> Dashboard
        </a>

        {{-- ── STOK ── --}}
        <div class="divider"></div>
        <div class="nav-section-label">Manajemen Stok</div>

        <a href="{{ route('ingredients.index') }}"
           class="nav-link {{ request()->routeIs('ingredients.*') ? 'active' : '' }}">
            <i class="fa-solid fa-box icon"></i> Bahan Baku
        </a>

        <a href="{{ route('stocks.index') }}"
           class="nav-link {{ request()->routeIs('stocks.*') ? 'active' : '' }}">
            <i class="fa-solid fa-right-left icon"></i> Transaksi Stok
        </a>

        @if(auth()->user()->isManager())
        <a href="{{ route('opnames.index') }}"
           class="nav-link {{ request()->routeIs('opnames.*') ? 'active' : '' }}">
            <i class="fa-solid fa-boxes-stacked icon"></i> Stock Opname
        </a>
        <a href="{{ route('categories.index') }}"
           class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <i class="fa-solid fa-tag icon"></i> Kategori
        </a>
        @endif

        {{-- ── MONITORING ── --}}
        <div class="divider"></div>
        <div class="nav-section-label">Monitoring Kualitas</div>

        <a href="{{ route('temperatures.index') }}"
           class="nav-link {{ request()->routeIs('temperatures.*') ? 'active' : '' }}">
            <i class="fa-solid fa-temperature-half icon"></i> Suhu Penyimpanan
        </a>

        <a href="{{ route('checklists.index') }}"
           class="nav-link {{ request()->routeIs('checklists.*') ? 'active' : '' }}">
            <i class="fa-solid fa-clipboard-check icon"></i> Checklist Kebersihan
        </a>

        @if(auth()->user()->isManager())
        <a href="{{ route('manager-tasks.index') }}"
           class="nav-link {{ request()->routeIs('manager-tasks.*') ? 'active' : '' }}">
            <i class="fa-solid fa-list-check icon"></i> Task SM & PIC
        </a>
        @endif

        {{-- ── JADWAL ── --}}
        <div class="divider"></div>
        <div class="nav-section-label">Penjadwalan</div>

        <a href="{{ route('shifts.index') }}"
           class="nav-link {{ request()->routeIs('shifts.*') ? 'active' : '' }}">
            <i class="fa-solid fa-code icon"></i> Kode Shift
        </a>

        <a href="{{ route('schedules.index') }}"
           class="nav-link {{ request()->routeIs('schedules.*') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-week icon"></i> Jadwal Mingguan
        </a>

        <a href="{{ route('attendances.index') }}"
           class="nav-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
            <i class="fa-solid fa-triangle-exclamation icon"></i> Kendala Kehadiran
        </a>

        {{-- ── STAFF — SM only ── --}}
        @if(auth()->user()->isSM())
        <div class="divider"></div>
        <div class="nav-section-label">Manajemen</div>
        <a href="{{ route('staff.index') }}"
           class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users icon"></i> Data Staff
        </a>
        @endif

    </div>

    {{-- Footer user info --}}
    <div class="sidebar-footer">
        <div class="user-chip">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <div class="user-name">{{ Str::limit(auth()->user()->name, 16) }}</div>
                <div style="margin-top:3px;">
                    <span class="role-chip role-{{ auth()->user()->role }}">
                        {{ auth()->user()->role }}
                    </span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin-left:auto;">
                @csrf
                <button type="submit" title="Logout"
                    style="background:none;border:none;cursor:pointer;color:#64748b;font-size:.9rem;">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>
</nav>

{{-- ═══ MAIN ═══ --}}
<div id="main">
    <div class="topbar">
        <div>
            <h1>@yield('title', 'Dashboard')</h1>
            <div class="breadcrumb">@yield('breadcrumb', 'SIMOR / Dashboard')</div>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm text-muted">
                <i class="fa-regular fa-clock"></i>
                {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </span>
        </div>
    </div>

    <div class="content">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-xmark"></i>
                <div>
                    @foreach($errors->all() as $e)
                        <div>{{ $e }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>
