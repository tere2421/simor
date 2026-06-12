@extends('layouts.app')
@section('title', $ingredient->name)
@section('breadcrumb', 'SIMOR / Stok / Bahan Baku / Detail')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="font-bold" style="font-size:1.25rem;color:#1e293b;">{{ $ingredient->name }}</h2>
    <div class="flex gap-2">
        <a href="{{ route('stocks.create') }}?ingredient={{ $ingredient->id }}" class="btn btn-primary">
            <i class="fa-solid fa-right-left"></i> Catat Transaksi
        </a>
        <a href="{{ route('ingredients.edit', $ingredient) }}" class="btn btn-warning">
            <i class="fa-solid fa-pen"></i> Edit
        </a>
        <a href="{{ route('ingredients.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="grid grid-cols-2 gap-4 mb-4">
    {{-- Info Card --}}
    <div class="card">
        <div class="card-header"><h3>Informasi Bahan Baku</h3></div>
        <div class="card-body">
            @php $status = $ingredient->stockStatus(); @endphp
            <table style="width:100%">
                <tr><td style="padding:.6rem 0;color:#64748b;width:45%">Kategori</td>
                    <td><span class="badge badge-info">{{ $ingredient->category->name }}</span></td></tr>
                <tr><td style="padding:.6rem 0;color:#64748b;">Satuan</td>
                    <td class="font-semibold">{{ $ingredient->unit }}</td></tr>
                <tr><td style="padding:.6rem 0;color:#64748b;">Stok Saat Ini</td>
                    <td>
                        <span style="font-size:1.4rem;font-weight:800;color:{{ $status === 'aman' ? '#22c55e' : ($status === 'rendah' ? '#f59e0b' : '#ef4444') }}">
                            {{ $ingredient->current_stock }} {{ $ingredient->unit }}
                        </span>
                    </td></tr>
                <tr><td style="padding:.6rem 0;color:#64748b;">Min. Threshold</td>
                    <td class="font-semibold">{{ $ingredient->min_stock_threshold }} {{ $ingredient->unit }}</td></tr>
                <tr><td style="padding:.6rem 0;color:#64748b;">Harga Satuan</td>
                    <td>{{ $ingredient->unit_price ? 'Rp '.number_format($ingredient->unit_price,0,',','.') : '-' }}</td></tr>
                <tr><td style="padding:.6rem 0;color:#64748b;">Lokasi</td>
                    <td>{{ $ingredient->storage_location ?? '-' }}</td></tr>
                <tr><td style="padding:.6rem 0;color:#64748b;">Kadaluarsa</td>
                    <td>
                        @if($ingredient->expiry_date)
                            @php $days = $ingredient->expiryDaysLeft(); @endphp
                            <span class="badge {{ $days < 0 ? 'badge-danger' : ($days <= 3 ? 'badge-warning' : 'badge-success') }}">
                                {{ $ingredient->expiry_date->format('d/m/Y') }} (H-{{ max(0,$days) }})
                            </span>
                        @else <span class="text-muted">-</span> @endif
                    </td></tr>
                <tr><td style="padding:.6rem 0;color:#64748b;">Status</td>
                    <td>
                        <span class="badge {{ $status === 'aman' ? 'badge-success' : ($status === 'rendah' ? 'badge-warning' : 'badge-danger') }}">
                            {{ ucfirst($status) }}
                        </span>
                    </td></tr>
            </table>
        </div>
    </div>

    {{-- Progress gauge --}}
    <div class="card">
        <div class="card-header"><h3>Level Stok</h3></div>
        <div class="card-body text-center" style="padding:2rem;">
            @php
                $pct = $ingredient->min_stock_threshold > 0
                    ? min(100, round(($ingredient->current_stock / ($ingredient->min_stock_threshold * 2)) * 100))
                    : 100;
                $color = $pct >= 70 ? '#22c55e' : ($pct >= 35 ? '#f59e0b' : '#ef4444');
            @endphp
            <div style="font-size:3rem;font-weight:900;color:{{ $color }}">{{ $pct }}%</div>
            <div class="text-muted text-sm">dari 2× threshold minimum</div>
            <div style="background:#f1f5f9;border-radius:9999px;height:14px;margin-top:1rem;overflow:hidden;">
                <div style="background:{{ $color }};height:100%;width:{{ $pct }}%;transition:width .5s;border-radius:9999px;"></div>
            </div>
            <div style="display:flex;justify-content:space-between;margin-top:.5rem;" class="text-xs text-muted">
                <span>0</span>
                <span>Threshold: {{ $ingredient->min_stock_threshold }}</span>
                <span>{{ $ingredient->min_stock_threshold * 2 }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Riwayat transaksi --}}
<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Transaksi</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>Waktu</th><th>Tipe</th><th>Jumlah</th>
                <th>Sebelum</th><th>Sesudah</th><th>Catatan</th><th>Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
                <tr>
                    <td class="text-xs text-muted">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="badge {{ $tx->type === 'in' ? 'badge-success' : 'badge-danger' }}">
                            <i class="fa-solid fa-arrow-{{ $tx->type === 'in' ? 'down' : 'up' }}"></i>
                            {{ $tx->type === 'in' ? 'MASUK' : 'KELUAR' }}
                        </span>
                    </td>
                    <td class="font-bold">{{ $tx->quantity }} {{ $ingredient->unit }}</td>
                    <td class="text-muted">{{ $tx->stock_before }}</td>
                    <td class="font-semibold">{{ $tx->stock_after }}</td>
                    <td class="text-xs">{{ $tx->notes ?? '-' }}</td>
                    <td class="text-xs">{{ $tx->user->name }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted" style="padding:2rem;">Belum ada transaksi</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="card-body" style="border-top:1px solid #f1f5f9;">{{ $transactions->links() }}</div>
</div>
@endsection
