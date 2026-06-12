@extends('layouts.app')
@section('title', 'Dashboard')
@section('breadcrumb', 'SIMOR / Dashboard')

@section('content')

{{-- ══ GREETING BAR ══ --}}
@php
    $hour = now()->hour;
    $greet = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 19 ? 'Selamat Sore' : 'Selamat Malam'));
    $roleColor = ['SM' => '#f59e0b', 'PIC' => '#3b82f6', 'Staff' => '#22c55e'][$user->role] ?? '#64748b';
    $roleBg    = ['SM' => '#fffbeb', 'PIC' => '#eff6ff', 'Staff' => '#f0fdf4'][$user->role] ?? '#f1f5f9';
@endphp
<div style="background:linear-gradient(135deg,#0f2035,#1a3350);border-radius:.75rem;padding:1.5rem 2rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;">
    <div>
        <div style="color:#94a3b8;font-size:.82rem;margin-bottom:.25rem;">{{ $greet }},</div>
        <div style="color:#fff;font-size:1.4rem;font-weight:800;">{{ $user->name }}</div>
        <div style="margin-top:.5rem;display:flex;align-items:center;gap:.75rem;">
            <span style="background:{{ $roleColor }};color:#fff;padding:3px 10px;border-radius:4px;font-size:.75rem;font-weight:700;">
                {{ $user->role }}
            </span>
            <span id="liveDate" style="color:#64748b;font-size:.8rem;"></span>
        </div>
    </div>
    <div style="text-align:right;">
        <div id="liveClock" style="font-size:2rem;font-weight:900;color:#fff;">
        00:00:00
        </div>
        <div style="color:#64748b;font-size:.75rem;">WIB</div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════
     TAMPILAN SM & PIC — Full dashboard
     ════════════════════════════════════════════════════════════ --}}
@if($user->isManager())

{{-- Stat Cards --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="stat-card" style="border-left-color:#3b82f6;">
        <div class="stat-icon" style="background:#eff6ff;color:#3b82f6;"><i class="fa-solid fa-box-open"></i></div>
        <div><div class="stat-value">{{ $totalIngredients }}</div><div class="stat-label">Total Bahan Baku</div></div>
    </div>
    <div class="stat-card" style="border-left-color:#ef4444;">
        <div class="stat-icon" style="background:#fef2f2;color:#ef4444;"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div>
            <div class="stat-value" style="color:#ef4444;">{{ $criticalStocks->count() }}</div>
            <div class="stat-label">Stok Kritis</div>
        </div>
    </div>
    <div class="stat-card" style="border-left-color:#f59e0b;">
        <div class="stat-icon" style="background:#fffbeb;color:#f59e0b;"><i class="fa-solid fa-temperature-high"></i></div>
        <div><div class="stat-value" style="color:#f59e0b;">{{ $abnormalTemp }}</div><div class="stat-label">Suhu Abnormal Hari Ini</div></div>
    </div>
    <div class="stat-card" style="border-left-color:#22c55e;">
        <div class="stat-icon" style="background:#f0fdf4;color:#22c55e;"><i class="fa-solid fa-users"></i></div>
        <div><div class="stat-value" style="color:#22c55e;">{{ $totalStaff }}</div><div class="stat-label">Total Staff Aktif</div></div>
    </div>
</div>

<div class="grid gap-4 mb-6" style="grid-template-columns:1fr 1fr 1fr;">

    {{-- Stok Kritis --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-circle-exclamation" style="color:#ef4444"></i> Stok Kritis</h3>
            <a href="{{ route('ingredients.index', ['status'=>'kritis']) }}" class="btn btn-sm btn-secondary">Lihat</a>
        </div>
        @if($criticalStocks->isEmpty())
            <div class="card-body text-center" style="color:#22c55e;padding:2rem;">
                <i class="fa-solid fa-circle-check fa-2x" style="display:block;margin-bottom:.75rem;"></i>
                <div class="font-semibold">Semua stok aman!</div>
            </div>
        @else
            <div style="max-height:220px;overflow-y:auto;">
                @foreach($criticalStocks->take(6) as $item)
                    <div style="padding:.7rem 1.25rem;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <div class="font-semibold text-sm">{{ $item->name }}</div>
                            <div class="text-xs text-muted">{{ $item->category->name }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold" style="color:#ef4444;">{{ $item->current_stock }} {{ $item->unit }}</div>
                            <div class="text-xs text-muted">min: {{ $item->min_stock_threshold }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Kadaluarsa Alert --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-calendar-xmark" style="color:#f59e0b"></i> Mendekati Kadaluarsa</h3>
        </div>
        @if($expiryAlerts->isEmpty())
            <div class="card-body text-center" style="color:#22c55e;padding:2rem;">
                <i class="fa-solid fa-circle-check fa-2x" style="display:block;margin-bottom:.75rem;"></i>
                <div class="font-semibold">Tidak ada dalam 7 hari</div>
            </div>
        @else
            <div style="max-height:220px;overflow-y:auto;">
                @foreach($expiryAlerts as $item)
                    @php $daysLeft = $item->expiryDaysLeft(); @endphp
                    <div style="padding:.7rem 1.25rem;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <div class="font-semibold text-sm">{{ $item->name }}</div>
                            <div class="text-xs text-muted">{{ $item->expiry_date->format('d/m/Y') }}</div>
                        </div>
                        <span class="badge {{ $daysLeft <= 1 ? 'badge-danger' : ($daysLeft <= 3 ? 'badge-warning' : 'badge-info') }}">
                            H-{{ $daysLeft }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Checklist + Suhu --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-clipboard-check" style="color:#22c55e"></i> Checklist Hari Ini</h3>
            <a href="{{ route('checklists.index') }}" class="btn btn-sm btn-secondary">Isi</a>
        </div>
        <div class="card-body">
            @php $barColor = $checklistPct >= 80 ? '#22c55e' : ($checklistPct >= 50 ? '#f59e0b' : '#ef4444'); @endphp
            <div class="text-center" style="margin-bottom:1rem;">
                <div style="font-size:2.5rem;font-weight:800;color:{{ $barColor }}">{{ $checklistPct }}%</div>
                <div class="text-sm text-muted">{{ $doneChecklist }} / {{ $todayChecklist }} item selesai</div>
            </div>
            <div style="background:#f1f5f9;border-radius:9999px;height:10px;overflow:hidden;">
                <div style="background:{{ $barColor }};height:100%;width:{{ $checklistPct }}%;transition:width .5s;"></div>
            </div>
        </div>
        <div style="border-top:1px solid #f1f5f9;padding:1rem 1.25rem;">
            <div class="font-semibold text-xs" style="color:#64748b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.6rem;">Status Zona Suhu</div>
            @foreach($zones->take(4) as $zone)
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.45rem;">
                    <span class="text-xs font-semibold" style="color:#334155;">{{ $zone->name }}</span>
                    @if($zone->latestRecord)
                        <span class="badge {{ $zone->latestRecord->is_abnormal ? 'badge-danger' : 'badge-success' }}">
                            {{ $zone->latestRecord->temperature }}°C
                        </span>
                    @else
                        <span class="badge badge-gray">Belum dicatat</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Chart + Jadwal --}}
<div class="grid gap-4 mb-6" style="grid-template-columns:1.5fr 1fr;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-chart-bar" style="color:#3b82f6"></i> Level Stok Bahan Baku</h3>
            <a href="{{ route('ingredients.index') }}" class="btn btn-sm btn-secondary">Detail</a>
        </div>
        <div class="card-body"><canvas id="stockChart" style="max-height:260px;"></canvas></div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-calendar-day" style="color:#22c55e"></i> Jadwal Hari Ini</h3>
            <a href="{{ route('schedules.index') }}" class="btn btn-sm btn-secondary">Semua</a>
        </div>
        @if($todaySchedules->isEmpty())
            <div class="card-body text-center" style="color:#94a3b8;padding:2rem;">
                <i class="fa-regular fa-calendar fa-2x" style="display:block;margin-bottom:.75rem;"></i>
                Belum ada jadwal hari ini
            </div>
        @else
            <div style="max-height:300px;overflow-y:auto;">
                @foreach($todaySchedules as $sched)
                    <div style="padding:.75rem 1.25rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:.75rem;">
                        <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#1e40af);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:800;color:#fff;flex-shrink:0;">
                            {{ strtoupper(substr($sched->staffProfile->name,0,1)) }}
                        </div>
                        <div>
                            <div class="font-semibold text-sm">{{ $sched->staffProfile->name }}</div>
                            <div class="text-xs text-muted">{{ $sched->staffProfile->position }}</div>
                        </div>
                        <span class="badge {{ $sched->shift->name==='Shift Pagi' ? 'badge-info' : ($sched->shift->name==='Shift Siang' ? 'badge-warning' : 'badge-gray') }}" style="margin-left:auto;font-size:.68rem;">
                            {{ $sched->shift->name }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Transaksi terbaru --}}
<div class="card mb-6">
    <div class="card-header">
        <h3><i class="fa-solid fa-clock-rotate-left" style="color:#64748b"></i> Transaksi Stok Terbaru</h3>
        <a href="{{ route('stocks.index') }}" class="btn btn-sm btn-secondary">Lihat Semua</a>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Kode</th><th>Tipe</th><th>Tanggal</th><th>Item</th><th>Oleh</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $tx)
                <tr>
                    <td style="font-family:monospace;font-weight:700;font-size:.82rem;color:{{ $tx->type==='in' ? '#166534' : '#991b1b' }};">{{ $tx->transaction_code }}</td>
                    <td><span class="badge {{ $tx->type==='in' ? 'badge-success' : 'badge-danger' }}"><i class="fa-solid fa-arrow-{{ $tx->type==='in' ? 'down' : 'up' }}"></i> {{ $tx->type==='in' ? 'MASUK' : 'KELUAR' }}</span></td>
                    <td class="text-sm">{{ $tx->transaction_date->format('d/m/Y') }}</td>
                    <td><span class="badge badge-info">{{ $tx->lines->count() }} item</span></td>
                    <td class="text-xs">{{ $tx->user->name }}</td>
                    <td><a href="{{ route('stocks.show', $tx) }}" class="btn btn-sm btn-secondary"><i class="fa-solid fa-eye"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted" style="padding:2rem;">Belum ada transaksi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
const ctx = document.getElementById('stockChart');
const labels  = @json($stockChartData->pluck('name'));
const current = @json($stockChartData->pluck('current_stock'));
const minima  = @json($stockChartData->pluck('min_stock_threshold'));
const colors  = current.map((v,i) => v <= minima[i] ? '#ef4444' : v <= minima[i]*1.5 ? '#f59e0b' : '#22c55e');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label:'Stok Saat Ini', data:current, backgroundColor:colors, borderRadius:5, borderSkipped:false },
            { label:'Minimum', data:minima, type:'line', borderColor:'#ef4444', borderDash:[5,4], borderWidth:1.5, pointRadius:0, fill:false }
        ]
    },
    options: {
        responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ position:'bottom', labels:{ font:{ size:11 } } } },
        scales:{ x:{ grid:{ display:false } }, y:{ grid:{ color:'#f1f5f9' }, beginAtZero:true } }
    }
});
</script>
@endpush

{{-- ════════════════════════════════════════════════════════════
     TAMPILAN STAFF — Jadwal pribadi + tugas harian
     ════════════════════════════════════════════════════════════ --}}
@elseif($user->isStaff())

{{-- Jadwal minggu ini --}}
<div class="card mb-6">
    <div class="card-header">
        <h3><i class="fa-solid fa-calendar-week" style="color:#3b82f6"></i> Jadwal Kamu Minggu Ini</h3>
        <a href="{{ route('schedules.index') }}" class="btn btn-sm btn-secondary">Lihat Semua</a>
    </div>
    @if(empty($mySchedule) || $mySchedule->isEmpty())
        <div class="card-body text-center" style="color:#94a3b8;padding:2.5rem;">
            <i class="fa-regular fa-calendar fa-2x" style="display:block;margin-bottom:.75rem;"></i>
            Belum ada jadwal untuk minggu ini.<br>
            <span class="text-xs">Hubungi SM atau PIC kamu.</span>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr><th>Hari</th><th>Tanggal</th><th>Shift</th><th>Jam</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @foreach($mySchedule as $sched)
                    <tr style="{{ $sched->schedule_date->isToday() ? 'background:#eff6ff;' : '' }}">
                        <td class="font-semibold">
                            {{ $sched->schedule_date->locale('id')->isoFormat('dddd') }}
                            @if($sched->schedule_date->isToday())
                                <span class="badge badge-info" style="margin-left:4px;font-size:.65rem;">Hari ini</span>
                            @endif
                        </td>
                        <td>{{ $sched->schedule_date->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge {{ $sched->shift->name==='Shift Pagi' ? 'badge-info' : ($sched->shift->name==='Shift Siang' ? 'badge-warning' : 'badge-gray') }}">
                                {{ $sched->shift->name }}
                            </span>
                        </td>
                        <td class="text-sm">
                            {{ \Carbon\Carbon::parse($sched->shift->start_time)->format('H:i') }} –
                            {{ \Carbon\Carbon::parse($sched->shift->end_time)->format('H:i') }}
                        </td>
                        <td>
                            <span class="badge {{ $sched->status==='approved' ? 'badge-success' : 'badge-warning' }}">
                                {{ $sched->status==='approved' ? 'Confirmed' : 'Pending' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Tugas harian staff --}}
<div class="grid grid-cols-2 gap-4 mb-6">

    {{-- Checklist --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-clipboard-check" style="color:#22c55e"></i> Checklist Hari Ini</h3>
            <a href="{{ route('checklists.index') }}" class="btn btn-sm btn-primary">
                <i class="fa-solid fa-pen"></i> Isi Sekarang
            </a>
        </div>
        <div class="card-body text-center">
            @php $barColor = $checklistPct >= 80 ? '#22c55e' : ($checklistPct >= 50 ? '#f59e0b' : '#ef4444'); @endphp
            <div style="font-size:3rem;font-weight:900;color:{{ $barColor }}">{{ $checklistPct }}%</div>
            <div class="text-sm text-muted">{{ $doneChecklist }}/{{ $todayChecklist }} item selesai</div>
            <div style="background:#f1f5f9;border-radius:9999px;height:8px;margin-top:.75rem;overflow:hidden;">
                <div style="background:{{ $barColor }};height:100%;width:{{ $checklistPct }}%;border-radius:9999px;"></div>
            </div>
            @if($checklistPct < 100)
            <a href="{{ route('checklists.index') }}" class="btn btn-primary" style="margin-top:1rem;width:100%;justify-content:center;">
                <i class="fa-solid fa-arrow-right"></i> Lanjutkan Checklist
            </a>
            @else
            <div style="margin-top:1rem;color:#22c55e;font-weight:700;font-size:.9rem;">
                <i class="fa-solid fa-party-horn"></i> Semua checklist selesai!
            </div>
            @endif
        </div>
    </div>

    {{-- Suhu --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-temperature-half" style="color:#f59e0b"></i> Catat Suhu</h3>
            <a href="{{ route('temperatures.create') }}" class="btn btn-sm btn-primary">
                <i class="fa-solid fa-plus"></i> Catat
            </a>
        </div>
        <div style="padding:.5rem 0;">
            @foreach($zones as $zone)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:.65rem 1.25rem;border-bottom:1px solid #f1f5f9;">
                    <div>
                        <div class="font-semibold text-sm">{{ $zone->name }}</div>
                        <div class="text-xs text-muted">{{ $zone->min_temp }}° ~ {{ $zone->max_temp }}°C</div>
                    </div>
                    @if($zone->latestRecord)
                        <span class="badge {{ $zone->latestRecord->is_abnormal ? 'badge-danger' : 'badge-success' }}">
                            {{ $zone->latestRecord->temperature }}°C
                        </span>
                    @else
                        <span class="badge badge-gray">Belum dicatat</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Alert stok kritis untuk staff (read only) --}}
@if($criticalStocks->isNotEmpty() || $expiryAlerts->isNotEmpty())
<div class="card mb-6" style="border-left:4px solid #f59e0b;">
    <div class="card-header">
        <h3><i class="fa-solid fa-bell" style="color:#f59e0b"></i> Perhatian — Info Stok</h3>
    </div>
    <div class="card-body">
        @if($criticalStocks->isNotEmpty())
        <div style="margin-bottom:.75rem;">
            <div class="text-xs font-semibold" style="color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem;">Stok Kritis</div>
            <div style="display:flex;flex-wrap:wrap;gap:.4rem;">
                @foreach($criticalStocks as $item)
                    <span style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;padding:3px 10px;border-radius:4px;font-size:.75rem;font-weight:600;">
                        {{ $item->name }} — {{ $item->current_stock }} {{ $item->unit }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif
        @if($expiryAlerts->isNotEmpty())
        <div>
            <div class="text-xs font-semibold" style="color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem;">Kadaluarsa ≤ 3 Hari</div>
            <div style="display:flex;flex-wrap:wrap;gap:.4rem;">
                @foreach($expiryAlerts as $item)
                    <span style="background:#fffbeb;border:1px solid #fde68a;color:#854d0e;padding:3px 10px;border-radius:4px;font-size:.75rem;font-weight:600;">
                        {{ $item->name }} — H-{{ $item->expiryDaysLeft() }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif
        <div class="text-xs text-muted" style="margin-top:.75rem;">
            <i class="fa-solid fa-info-circle"></i> Informasikan ke PIC atau SM kamu.
        </div>
    </div>
</div>
@endif

{{-- Shortcut aksi harian staff --}}
<div class="grid grid-cols-3 gap-4">
    <a href="{{ route('stocks.create') }}" style="text-decoration:none;">
        <div class="card" style="padding:1.5rem;text-align:center;border-top:3px solid #3b82f6;transition:transform .15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
            <i class="fa-solid fa-right-left" style="font-size:1.75rem;color:#3b82f6;display:block;margin-bottom:.75rem;"></i>
            <div class="font-bold" style="color:#1e293b;">Input Transaksi</div>
            <div class="text-xs text-muted">Catat stok masuk / keluar</div>
        </div>
    </a>
    <a href="{{ route('temperatures.create') }}" style="text-decoration:none;">
        <div class="card" style="padding:1.5rem;text-align:center;border-top:3px solid #f59e0b;transition:transform .15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
            <i class="fa-solid fa-temperature-half" style="font-size:1.75rem;color:#f59e0b;display:block;margin-bottom:.75rem;"></i>
            <div class="font-bold" style="color:#1e293b;">Catat Suhu</div>
            <div class="text-xs text-muted">Monitoring penyimpanan</div>
        </div>
    </a>
    <a href="{{ route('checklists.index') }}" style="text-decoration:none;">
        <div class="card" style="padding:1.5rem;text-align:center;border-top:3px solid #22c55e;transition:transform .15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
            <i class="fa-solid fa-clipboard-check" style="font-size:1.75rem;color:#22c55e;display:block;margin-bottom:.75rem;"></i>
            <div class="font-bold" style="color:#1e293b;">Checklist</div>
            <div class="text-xs text-muted">Kebersihan & operasional</div>
        </div>
    </a>
</div>

@endif

@push('scripts')
<script>
function updateDateTime() {
    const now = new Date();

    const time = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });

    const date = now.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });

    document.getElementById('liveClock').innerText = time;
    document.getElementById('liveDate').innerText = date;
}

updateDateTime();
setInterval(updateDateTime, 1000);
</script>
@endpush

@endsection
