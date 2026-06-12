@extends('layouts.app')
@section('title','Monitoring Suhu')
@section('breadcrumb','SIMOR / Monitoring / Suhu Penyimpanan')
@section('content')

<div class="flex items-center justify-between mb-6">
    <h2 class="font-bold" style="font-size:1.25rem;">Monitoring Suhu Penyimpanan</h2>
    <div class="flex gap-2">
        <a href="{{ route('temperatures.zones') }}" class="btn btn-secondary"><i class="fa-solid fa-map-marker-alt"></i> Kelola Zona</a>
        <a href="{{ route('temperatures.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Catat Suhu</a>
    </div>
</div>

{{-- Status Semua Zona --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    @foreach($zoneStatus as $zone)
        @php $r = $zone->latestRecord; $ok = $r && !$r->is_abnormal; @endphp
        <div class="stat-card" style="border-left-color:{{ $r ? ($ok ? '#22c55e' : '#ef4444') : '#94a3b8' }};">
            <div class="stat-icon" style="background:{{ $r ? ($ok ? '#f0fdf4' : '#fef2f2') : '#f1f5f9' }};color:{{ $r ? ($ok ? '#22c55e' : '#ef4444') : '#94a3b8' }};">
                <i class="fa-solid fa-temperature-{{ $r && $r->temperature > 0 ? 'high' : 'low' }}"></i>
            </div>
            <div>
                <div class="stat-value" style="font-size:1.4rem;color:{{ $r ? ($ok ? '#22c55e' : '#ef4444') : '#94a3b8' }}">
                    {{ $r ? $r->temperature.'°C' : 'N/A' }}
                </div>
                <div class="stat-label">{{ $zone->name }}</div>
                <div class="text-xs text-muted">{{ $zone->min_temp }}° ~ {{ $zone->max_temp }}°C</div>
            </div>
        </div>
    @endforeach
</div>

{{-- Filter --}}
<div class="card mb-4">
    <form method="GET" class="card-body" style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;">
        <div style="flex:1;min-width:150px;">
            <label class="form-label">Zona</label>
            <select name="zone_id" class="form-control">
                <option value="">Semua Zona</option>
                @foreach($zones as $z)
                    <option value="{{ $z->id }}" {{ request('zone_id') == $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1;min-width:150px;">
            <label class="form-label">Tanggal</label>
            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div style="flex:1;min-width:140px;">
            <label class="form-label">Status</label>
            <select name="abnormal" class="form-control">
                <option value="">Semua</option>
                <option value="1" {{ request('abnormal') ? 'selected' : '' }}>Hanya Abnormal</option>
            </select>
        </div>
        <button class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        <a href="{{ route('temperatures.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr><th>Waktu</th><th>Zona</th><th>Suhu</th><th>Range Normal</th><th>Status</th><th>Catatan</th><th>Oleh</th></tr>
        </thead>
        <tbody>
            @forelse($records as $rec)
            <tr>
                <td>
                    <div class="font-semibold text-sm">{{ $rec->recorded_at->format('d/m/Y') }}</div>
                    <div class="text-xs text-muted">{{ $rec->recorded_at->format('H:i') }}</div>
                </td>
                <td class="font-semibold">{{ $rec->zone->name }}<br><span class="text-xs text-muted">{{ $rec->zone->location }}</span></td>
                <td class="font-bold" style="font-size:1.1rem;color:{{ $rec->is_abnormal ? '#ef4444' : '#22c55e' }}">{{ $rec->temperature }}°C</td>
                <td class="text-xs text-muted">{{ $rec->zone->min_temp }}° ~ {{ $rec->zone->max_temp }}°C</td>
                <td>
                    @if($rec->is_abnormal)
                        <span class="badge badge-danger"><i class="fa-solid fa-triangle-exclamation"></i> ABNORMAL</span>
                    @else
                        <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> NORMAL</span>
                    @endif
                </td>
                <td class="text-xs">{{ $rec->notes ?? '-' }}</td>
                <td class="text-xs">{{ $rec->user->name }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted" style="padding:3rem;">
                <i class="fa-solid fa-temperature-empty fa-2x" style="display:block;margin-bottom:.75rem;opacity:.3;"></i>
                Belum ada data suhu
            </td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="card-body" style="border-top:1px solid #f1f5f9;">{{ $records->links() }}</div>
</div>
@endsection
