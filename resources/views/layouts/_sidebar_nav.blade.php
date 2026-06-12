{{-- GANTI bagian navigasi sidebar di resources/views/layouts/app.blade.php --}}
{{-- Cari tag <div style="overflow-y:auto;flex:1 dan replace seluruh blok navnya --}}

{{-- COPY BAGIAN INI (dari <div style="overflow-y:auto...) sampai penutup </div> sebelum sidebar-footer --}}

<div style="overflow-y:auto;flex:1;padding-bottom:1rem;">

    <div class="nav-section-label">Utama</div>
    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge-high icon"></i> Dashboard
    </a>

    <div class="divider"></div>
    <div class="nav-section-label">Manajemen Stok</div>
    <a href="{{ route('ingredients.index') }}" class="nav-link {{ request()->routeIs('ingredients.*') ? 'active' : '' }}">
        <i class="fa-solid fa-box icon"></i> Bahan Baku
    </a>
    <a href="{{ route('stocks.index') }}" class="nav-link {{ request()->routeIs('stocks.*') ? 'active' : '' }}">
        <i class="fa-solid fa-right-left icon"></i> Transaksi Stok
    </a>
    @if(auth()->user()->isManager())
    <a href="{{ route('opnames.index') }}" class="nav-link {{ request()->routeIs('opnames.*') ? 'active' : '' }}">
        <i class="fa-solid fa-boxes-stacked icon"></i> Stock Opname
    </a>
    <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
        <i class="fa-solid fa-tag icon"></i> Kategori
    </a>
    @endif

    <div class="divider"></div>
    <div class="nav-section-label">Monitoring Kualitas</div>
    <a href="{{ route('temperatures.index') }}" class="nav-link {{ request()->routeIs('temperatures.*') ? 'active' : '' }}">
        <i class="fa-solid fa-temperature-half icon"></i> Suhu Penyimpanan
    </a>
    <a href="{{ route('checklists.index') }}" class="nav-link {{ request()->routeIs('checklists.*') ? 'active' : '' }}">
        <i class="fa-solid fa-clipboard-check icon"></i> Checklist Kebersihan
    </a>
    @if(auth()->user()->isManager())
    <a href="{{ route('manager-tasks.index') }}" class="nav-link {{ request()->routeIs('manager-tasks.*') ? 'active' : '' }}">
        <i class="fa-solid fa-list-check icon"></i> Task SM & PIC
    </a>
    @endif

    <div class="divider"></div>
    <div class="nav-section-label">Penjadwalan</div>
    <a href="{{ route('shifts.index') }}" class="nav-link {{ request()->routeIs('shifts.*') ? 'active' : '' }}">
        <i class="fa-solid fa-code icon"></i> Kode Shift
    </a>
    <a href="{{ route('schedules.index') }}" class="nav-link {{ request()->routeIs('schedules.*') ? 'active' : '' }}">
        <i class="fa-solid fa-calendar-week icon"></i> Jadwal Mingguan
    </a>
    <a href="{{ route('attendances.index') }}" class="nav-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
        <i class="fa-solid fa-triangle-exclamation icon"></i> Kendala Kehadiran
    </a>

    @if(auth()->user()->isSM())
    <div class="divider"></div>
    <div class="nav-section-label">Manajemen</div>
    <a href="{{ route('staff.index') }}" class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}">
        <i class="fa-solid fa-users icon"></i> Data Staff
    </a>
    @endif

</div>
