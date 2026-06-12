@extends('layouts.app')
@section('title', 'Bahan Baku')
@section('breadcrumb', 'SIMOR / Stok / Bahan Baku')

@section('content')
{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="font-bold" style="font-size:1.25rem;color:#1e293b;">Manajemen Bahan Baku</h2>
        <p class="text-sm text-muted">Total {{ $ingredients->total() }} bahan baku aktif</p>
    </div>
    <a href="{{ route('ingredients.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Bahan Baku
    </a>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <form method="GET" class="card-body" style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;">
        <div style="flex:2;min-width:200px;">
            <label class="form-label">Cari</label>
            <input type="text" name="search" class="form-control" placeholder="Nama bahan baku..." value="{{ request('search') }}">
        </div>
        <div style="flex:1;min-width:160px;">
            <label class="form-label">Kategori</label>
            <select name="category_id" class="form-control">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1;min-width:140px;">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="kritis" {{ request('status') === 'kritis' ? 'selected' : '' }}>Kritis / Habis</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-magnifying-glass"></i> Cari
        </button>
        <a href="{{ route('ingredients.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Bahan Baku</th>
                    <th>Kategori</th>
                    <th>Stok Saat Ini</th>
                    <th>Min. Threshold</th>
                    <th>Lokasi</th>
                    <th>Kadaluarsa</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ingredients as $i => $item)
                    <tr>
                        <td class="text-muted text-xs">{{ $ingredients->firstItem() + $i }}</td>
                        <td>
                            <div class="font-semibold">{{ $item->name }}</div>
                            @if($item->unit_price)
                                <div class="text-xs text-muted">Rp {{ number_format($item->unit_price, 0, ',', '.') }} / {{ $item->unit }}</div>
                            @endif
                        </td>
                        <td><span class="badge badge-info">{{ $item->category->name }}</span></td>
                        <td class="font-bold {{ $item->isCritical() ? 'text-danger' : '' }}" style="color:{{ $item->isCritical() ? '#ef4444' : 'inherit' }}">
                            {{ $item->current_stock }} {{ $item->unit }}
                        </td>
                        <td class="text-muted">{{ $item->min_stock_threshold }} {{ $item->unit }}</td>
                        <td class="text-xs">{{ $item->storage_location ?? '-' }}</td>
                        <td>
                            @if($item->expiry_date)
                                @php $days = $item->expiryDaysLeft(); @endphp
                                <span class="badge {{ $days < 0 ? 'badge-danger' : ($days <= 3 ? 'badge-warning' : 'badge-success') }}">
                                    {{ $item->expiry_date->format('d/m/Y') }}
                                    @if($days >= 0) (H-{{ $days }}) @else (KADALUARSA) @endif
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @php $status = $item->stockStatus(); @endphp
                            <span class="badge {{ $status === 'aman' ? 'badge-success' : ($status === 'rendah' ? 'badge-warning' : 'badge-danger') }}">
                                <i class="fa-solid fa-circle" style="font-size:.5em"></i>
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <a href="{{ route('ingredients.show', $item) }}" class="btn btn-sm btn-secondary" title="Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('ingredients.edit', $item) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form method="POST" action="{{ route('ingredients.destroy', $item) }}"
                                      onsubmit="return confirm('Nonaktifkan bahan baku ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Nonaktifkan">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted" style="padding:3rem;">
                            <i class="fa-solid fa-box-open fa-2x" style="display:block;margin-bottom:.75rem;opacity:.3;"></i>
                            Belum ada bahan baku
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body" style="border-top:1px solid #f1f5f9;">
        {{ $ingredients->links() }}
    </div>
</div>
@endsection
