@extends('layouts.app')
@section('title','Kelola Zona Penyimpanan')
@section('breadcrumb','SIMOR / Monitoring / Zona Penyimpanan')

@section('content')
@php
$typeConfig = [
    'chiller'     => ['🧊','Chiller',      '#eff6ff','#3b82f6'],
    'freezer'     => ['❄️','Freezer',      '#f0f9ff','#0ea5e9'],
    'dry_storage' => ['📦','Dry Storage',  '#fffbeb','#f59e0b'],
    'display'     => ['🏪','Display',      '#f0fdf4','#22c55e'],
    'other'       => ['🌡️','Lainnya',      '#f5f3ff','#8b5cf6'],
];
@endphp

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="font-bold" style="font-size:1.25rem;">Zona Penyimpanan</h2>
        <p class="text-sm text-muted">Kelola equipment chiller, freezer, dry storage, dan lainnya</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('temperatures.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-thermometer-half"></i> Riwayat Suhu
        </a>
        <a href="{{ route('temperatures.zones.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah Zona
        </a>
    </div>
</div>

{{-- Summary per tipe --}}
<div class="grid gap-4 mb-6" style="grid-template-columns:repeat(5,1fr);">
    @foreach($typeConfig as $type => [$icon,$label,$bg,$clr])
    @php $count = $zones->where('type', $type)->count(); @endphp
    <div class="stat-card" style="border-left-color:{{ $clr }};">
        <div class="stat-icon" style="background:{{ $bg }};font-size:1.3rem;">{{ $icon }}</div>
        <div>
            <div class="stat-value" style="color:{{ $clr }};">{{ $count }}</div>
            <div class="stat-label">{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Zone cards --}}
@foreach($typeConfig as $type => [$icon, $label, $bg, $clr])
    @php $typeZones = $zones->where('type', $type); @endphp
    @if($typeZones->isNotEmpty())
    <div style="margin-bottom:1.5rem;">
        <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.75rem;">
            <span style="font-size:1.1rem;">{{ $icon }}</span>
            <h3 class="font-bold" style="font-size:.95rem;color:#1e293b;">{{ $label }}</h3>
            <span class="badge badge-gray" style="font-size:.7rem;">{{ $typeZones->count() }} zona</span>
        </div>

        <div class="grid gap-4" style="grid-template-columns:repeat(3,1fr);">
            @foreach($typeZones as $zone)
            @php
                $latest   = $zone->latestRecord;
                $isOk     = $latest && !$latest->is_abnormal;
                $isAbnorm = $latest && $latest->is_abnormal;
                $statusClr = !$latest ? '#94a3b8' : ($isOk ? '#22c55e' : '#ef4444');
                $statusBg  = !$latest ? '#f1f5f9' : ($isOk ? '#f0fdf4' : '#fef2f2');
            @endphp
            <div class="card" style="border-top:3px solid {{ $clr }};">
                <div class="card-body" style="padding:1rem 1.25rem;">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <div class="font-bold" style="font-size:.95rem;color:#1e293b;">
                                {{ $zone->name }}
                            </div>
                            @if($zone->location)
                            <div class="text-xs text-muted">
                                <i class="fa-solid fa-location-dot" style="font-size:.65rem;"></i>
                                {{ $zone->location }}
                            </div>
                            @endif
                        </div>
                        {{-- Status suhu terkini --}}
                        <div style="background:{{ $statusBg }};border-radius:.5rem;
                                    padding:.4rem .75rem;text-align:center;min-width:64px;">
                            @if($latest)
                                <div style="font-size:1.1rem;font-weight:800;color:{{ $statusClr }};">
                                    {{ $latest->temperature }}°C
                                </div>
                                <div style="font-size:.62rem;color:{{ $statusClr }};">
                                    {{ $isOk ? 'Normal' : 'Abnormal' }}
                                </div>
                            @else
                                <div style="font-size:.72rem;color:#94a3b8;">Belum<br>dicatat</div>
                            @endif
                        </div>
                    </div>

                    {{-- Range --}}
                    <div style="background:#f8fafc;border-radius:.4rem;padding:.5rem .75rem;margin-bottom:.875rem;">
                        <div class="text-xs text-muted" style="margin-bottom:3px;">Range Normal</div>
                        <div style="display:flex;align-items:center;gap:.5rem;">
                            <span style="font-weight:700;color:#3b82f6;font-size:.9rem;">{{ $zone->min_temp }}°C</span>
                            <span style="color:#94a3b8;font-size:.8rem;">~</span>
                            <span style="font-weight:700;color:#ef4444;font-size:.9rem;">{{ $zone->max_temp }}°C</span>
                        </div>
                    </div>

                    @if($zone->description)
                    <div class="text-xs text-muted" style="margin-bottom:.875rem;">
                        {{ $zone->description }}
                    </div>
                    @endif

                    {{-- Riwayat records --}}
                    <div class="text-xs text-muted" style="margin-bottom:.875rem;">
                        <i class="fa-solid fa-clock-rotate-left" style="font-size:.65rem;"></i>
                        {{ $zone->records_count }} pencatatan
                        @if($latest)
                            · Terakhir {{ $latest->recorded_at->diffForHumans() }}
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2">
                        <a href="{{ route('temperatures.create') }}?zone={{ $zone->id }}"
                           class="btn btn-sm btn-primary" style="flex:1;justify-content:center;">
                            <i class="fa-solid fa-plus"></i> Catat Suhu
                        </a>
                        <a href="{{ route('temperatures.zones.edit', $zone) }}"
                           class="btn btn-sm btn-warning">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <form method="POST" action="{{ route('temperatures.zones.destroy', $zone) }}"
                              onsubmit="return confirm('Hapus zona {{ $zone->name }}?\nZona hanya bisa dihapus jika belum ada riwayat pencatatan suhu.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
@endforeach

@if($zones->isEmpty())
<div class="card" style="padding:3rem;text-align:center;">
    <i class="fa-solid fa-snowflake fa-3x" style="color:#94a3b8;margin-bottom:1rem;display:block;"></i>
    <div class="font-semibold" style="color:#475569;margin-bottom:.5rem;">Belum ada zona penyimpanan</div>
    <p class="text-sm text-muted" style="margin-bottom:1.25rem;">Tambahkan zona chiller, freezer, atau dry storage</p>
    <a href="{{ route('temperatures.zones.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Zona Pertama
    </a>
</div>
@endif
@endsection
