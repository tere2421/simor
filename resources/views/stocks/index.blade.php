@extends('layouts.app')
@section('title', 'Transaksi Stok')
@section('breadcrumb', 'SIMOR / Stok / Transaksi')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="font-bold" style="font-size:1.25rem;color:#1e293b;">Riwayat Transaksi Stok</h2>
        <p class="text-sm text-muted">Setiap baris = satu sesi input (bisa berisi banyak item)</p>
    </div>
    <a href="{{ route('stocks.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Input Transaksi Baru
    </a>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <form method="GET" class="card-body" style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;">
        <div style="flex:2;min-width:180px;">
            <label class="form-label">Cari Kode / Catatan</label>
            <input type="text" name="search" class="form-control"
                   placeholder="IN-20250521-001..." value="{{ request('search') }}">
        </div>
        <div style="flex:1;min-width:140px;">
            <label class="form-label">Tipe</label>
            <select name="type" class="form-control">
                <option value="">Semua</option>
                <option value="in"  {{ request('type')==='in'  ? 'selected':'' }}>MASUK</option>
                <option value="out" {{ request('type')==='out' ? 'selected':'' }}>KELUAR</option>
            </select>
        </div>
        <div style="flex:1;min-width:140px;">
            <label class="form-label">Tanggal</label>
            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>
        <button class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
        <a href="{{ route('stocks.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

<div class="card">
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Kode Transaksi</th>
                    <th>Tipe</th>
                    <th>Tanggal</th>
                    <th>Jumlah Item</th>
                    <th>Item yang Diproses</th>
                    <th>Catatan</th>
                    <th>Oleh</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($headers as $h)
                <tr>
                    <td>
                        <span style="font-family:monospace;font-weight:700;font-size:.85rem;
                            color:{{ $h->type==='in' ? '#166534' : '#991b1b' }};">
                            {{ $h->transaction_code }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $h->type==='in' ? 'badge-success' : 'badge-danger' }}">
                            <i class="fa-solid fa-arrow-{{ $h->type==='in' ? 'down' : 'up' }}"></i>
                            {{ $h->type==='in' ? 'MASUK' : 'KELUAR' }}
                        </span>
                    </td>
                    <td>
                        <div class="font-semibold text-sm">{{ $h->transaction_date->format('d/m/Y') }}</div>
                        <div class="text-xs text-muted">{{ $h->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-info" style="font-size:.85rem;">
                            {{ $h->lines->count() }} item
                        </span>
                    </td>
                    <td style="max-width:260px;">
                        <div style="display:flex;flex-wrap:wrap;gap:4px;">
                            @foreach($h->lines->take(4) as $line)
                                <span style="background:#f1f5f9;color:#334155;padding:2px 8px;
                                    border-radius:4px;font-size:.72rem;font-weight:600;">
                                    {{ $line->ingredient->name }}
                                    <span style="color:{{ $h->type==='in' ? '#16a34a' : '#dc2626' }};">
                                        {{ $h->type==='in' ? '+' : '-' }}{{ $line->quantity }} {{ $line->ingredient->unit }}
                                    </span>
                                </span>
                            @endforeach
                            @if($h->lines->count() > 4)
                                <span style="background:#e2e8f0;color:#64748b;padding:2px 8px;
                                    border-radius:4px;font-size:.72rem;">
                                    +{{ $h->lines->count() - 4 }} lainnya
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="text-xs text-muted">{{ $h->notes ?? '—' }}</td>
                    <td class="text-xs">{{ $h->user->name }}</td>
                    <td>
                        <a href="{{ route('stocks.show', $h) }}" class="btn btn-sm btn-secondary">
                            <i class="fa-solid fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted" style="padding:3rem;">
                        <i class="fa-solid fa-right-left fa-2x" style="display:block;margin-bottom:.75rem;opacity:.3;"></i>
                        Belum ada transaksi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body" style="border-top:1px solid #f1f5f9;">
        {{ $headers->links() }}
    </div>
</div>
@endsection
