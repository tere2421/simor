@extends('layouts.app')
@section('title', $stock->transaction_code)
@section('breadcrumb', 'SIMOR / Stok / Transaksi / Detail')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="font-bold" style="font-size:1.25rem;color:#1e293b;">
            Detail Transaksi
            <span style="font-family:monospace;color:{{ $stock->type==='in' ? '#166534' : '#991b1b' }};">
                {{ $stock->transaction_code }}
            </span>
        </h2>
        <p class="text-sm text-muted">
            {{ $stock->transaction_date->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            · Dicatat oleh {{ $stock->user->name }}
            · {{ $stock->created_at->diffForHumans() }}
        </p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('stocks.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- Header info --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="stat-card" style="border-left-color:{{ $stock->type==='in' ? '#22c55e' : '#ef4444' }};">
        <div class="stat-icon" style="background:{{ $stock->type==='in' ? '#f0fdf4' : '#fef2f2' }};
             color:{{ $stock->type==='in' ? '#22c55e' : '#ef4444' }};">
            <i class="fa-solid fa-arrow-{{ $stock->type==='in' ? 'down' : 'up' }}"></i>
        </div>
        <div>
            <div class="stat-value" style="font-size:1.1rem;color:{{ $stock->type==='in' ? '#166534' : '#991b1b' }};">
                {{ $stock->type==='in' ? 'STOK MASUK' : 'STOK KELUAR' }}
            </div>
            <div class="stat-label">Tipe Transaksi</div>
        </div>
    </div>
    <div class="stat-card" style="border-left-color:#3b82f6;">
        <div class="stat-icon" style="background:#eff6ff;color:#3b82f6;">
            <i class="fa-solid fa-boxes-stacked"></i>
        </div>
        <div>
            <div class="stat-value">{{ $stock->lines->count() }}</div>
            <div class="stat-label">Total Item</div>
        </div>
    </div>
    <div class="stat-card" style="border-left-color:#f59e0b;">
        <div class="stat-icon" style="background:#fffbeb;color:#f59e0b;">
            <i class="fa-solid fa-calendar-day"></i>
        </div>
        <div>
            <div class="stat-value" style="font-size:1.1rem;">{{ $stock->transaction_date->format('d/m/Y') }}</div>
            <div class="stat-label">Tanggal</div>
        </div>
    </div>
    <div class="stat-card" style="border-left-color:#8b5cf6;">
        <div class="stat-icon" style="background:#f5f3ff;color:#8b5cf6;">
            <i class="fa-solid fa-user"></i>
        </div>
        <div>
            <div class="stat-value" style="font-size:.95rem;">{{ Str::limit($stock->user->name, 16) }}</div>
            <div class="stat-label">Dicatat Oleh</div>
        </div>
    </div>
</div>

@if($stock->notes)
<div class="alert alert-warning mb-4" style="background:#fffbeb;border:1px solid #fde68a;color:#92400e;padding:.875rem 1.25rem;border-radius:.5rem;display:flex;gap:.75rem;align-items:center;margin-bottom:1.25rem;">
    <i class="fa-solid fa-note-sticky"></i>
    <span><strong>Catatan:</strong> {{ $stock->notes }}</span>
</div>
@endif

{{-- Lines table --}}
<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-list" style="color:#3b82f6;"></i> Rincian Item</h3>
        <span class="badge badge-info">{{ $stock->lines->count() }} item</span>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Bahan Baku</th>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                    <th>Stok Sebelum</th>
                    <th>Stok Sesudah</th>
                    <th>Perubahan</th>
                    <th>Catatan Item</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stock->lines as $i => $line)
                <tr>
                    <td class="text-muted text-xs">{{ $i + 1 }}</td>
                    <td class="font-semibold">{{ $line->ingredient->name }}</td>
                    <td>
                        <span class="badge badge-info">{{ $line->ingredient->category->name }}</span>
                    </td>
                    <td class="font-bold" style="color:{{ $stock->type==='in' ? '#166534' : '#991b1b' }};">
                        {{ $stock->type==='in' ? '+' : '-' }}{{ $line->quantity }} {{ $line->ingredient->unit }}
                    </td>
                    <td class="text-muted">{{ $line->stock_before }} {{ $line->ingredient->unit }}</td>
                    <td class="font-semibold">
                        @php
                            $isLow = $line->stock_after <= $line->ingredient->min_stock_threshold;
                        @endphp
                        <span style="color:{{ $isLow ? '#ef4444' : '#22c55e' }};">
                            {{ $line->stock_after }} {{ $line->ingredient->unit }}
                        </span>
                        @if($isLow)
                            <span class="badge badge-danger" style="margin-left:4px;font-size:.65rem;">KRITIS</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $diff = $line->stock_after - $line->stock_before;
                            $pct  = $line->stock_before > 0
                                ? round(abs($diff) / $line->stock_before * 100)
                                : 100;
                        @endphp
                        <div style="display:flex;align-items:center;gap:.5rem;">
                            <div style="width:60px;height:6px;background:#f1f5f9;border-radius:9999px;overflow:hidden;">
                                <div style="width:{{ min(100, $pct) }}%;height:100%;
                                    background:{{ $diff > 0 ? '#22c55e' : '#ef4444' }};border-radius:9999px;"></div>
                            </div>
                            <span style="font-size:.75rem;font-weight:700;color:{{ $diff > 0 ? '#166534' : '#991b1b' }};">
                                {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                            </span>
                        </div>
                    </td>
                    <td class="text-xs text-muted">{{ $line->notes ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
