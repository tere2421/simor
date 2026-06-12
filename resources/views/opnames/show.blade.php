@extends('layouts.app')
@section('title', $opname->opname_code)
@section('breadcrumb','SIMOR / Stok / Stock Opname / Detail')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="font-bold" style="font-size:1.25rem;">
            {{ $opname->opname_code }}
            <span class="badge {{ $opname->status==='approved' ? 'badge-success' : ($opname->status==='submitted' ? 'badge-warning' : 'badge-gray') }}" style="vertical-align:middle;margin-left:.5rem;">
                {{ strtoupper($opname->status) }}
            </span>
        </h2>
        <p class="text-sm text-muted">Periode {{ $opname->period }} · {{ $opname->opname_date->format('d/m/Y') }} · {{ $opname->user->name }}</p>
    </div>
    <div class="flex gap-2">
        @if($opname->status !== 'approved' && auth()->user()->isManager())
            <form method="POST" action="{{ route('opnames.approve', $opname) }}"
                  onsubmit="return confirm('Approve opname ini? Semua stok akan diperbarui sesuai stok fisik.')">
                @csrf @method('PATCH')
                <button class="btn btn-success">
                    <i class="fa-solid fa-check-double"></i> Approve & Update Stok
                </button>
            </form>
        @endif
        <a href="{{ route('opnames.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

@if($opname->notes)
<div class="alert alert-warning mb-4"><i class="fa-solid fa-note-sticky"></i> {{ $opname->notes }}</div>
@endif

{{-- Stats --}}
@php
    $plus  = $opname->lines->filter(fn($l) => $l->difference > 0)->count();
    $minus = $opname->lines->filter(fn($l) => $l->difference < 0)->count();
    $ok    = $opname->lines->filter(fn($l) => $l->difference == 0)->count();
@endphp
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="stat-card" style="border-left-color:#3b82f6;">
        <div class="stat-icon" style="background:#eff6ff;color:#3b82f6;"><i class="fa-solid fa-boxes-stacked"></i></div>
        <div><div class="stat-value">{{ $opname->lines->count() }}</div><div class="stat-label">Total Item</div></div>
    </div>
    <div class="stat-card" style="border-left-color:#22c55e;">
        <div class="stat-icon" style="background:#f0fdf4;color:#22c55e;"><i class="fa-solid fa-equals"></i></div>
        <div><div class="stat-value" style="color:#22c55e;">{{ $ok }}</div><div class="stat-label">Sesuai Sistem</div></div>
    </div>
    <div class="stat-card" style="border-left-color:#f59e0b;">
        <div class="stat-icon" style="background:#fffbeb;color:#f59e0b;"><i class="fa-solid fa-arrow-up"></i></div>
        <div><div class="stat-value" style="color:#f59e0b;">{{ $plus }}</div><div class="stat-label">Lebih dari Sistem</div></div>
    </div>
    <div class="stat-card" style="border-left-color:#ef4444;">
        <div class="stat-icon" style="background:#fef2f2;color:#ef4444;"><i class="fa-solid fa-arrow-down"></i></div>
        <div><div class="stat-value" style="color:#ef4444;">{{ $minus }}</div><div class="stat-label">Kurang dari Sistem</div></div>
    </div>
</div>

<div class="card">
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>#</th><th>Bahan Baku</th><th>Kategori</th><th>Stok Sistem</th><th>Stok Fisik</th><th>Selisih</th><th>Catatan</th></tr>
            </thead>
            <tbody>
                @foreach($opname->lines as $i => $line)
                @php $diff = $line->difference; @endphp
                <tr style="{{ abs($diff) > 0 ? 'background:'.($diff < 0 ? '#fff8f8' : '#f8fff8').';' : '' }}">
                    <td class="text-muted text-xs">{{ $i+1 }}</td>
                    <td class="font-semibold">{{ $line->ingredient->name }}</td>
                    <td><span class="badge badge-info">{{ $line->ingredient->category->name }}</span></td>
                    <td class="text-muted">{{ $line->system_stock }} {{ $line->ingredient->unit }}</td>
                    <td class="font-bold">{{ $line->actual_stock }} {{ $line->ingredient->unit }}</td>
                    <td>
                        @if($diff == 0)
                            <span class="badge badge-success">±0</span>
                        @elseif($diff > 0)
                            <span class="badge badge-warning">+{{ $diff }} {{ $line->ingredient->unit }}</span>
                        @else
                            <span class="badge badge-danger">{{ $diff }} {{ $line->ingredient->unit }}</span>
                        @endif
                    </td>
                    <td class="text-xs text-muted">{{ $line->notes ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
