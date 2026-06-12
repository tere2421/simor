@extends('layouts.app')
@section('title', 'Edit Bahan Baku')
@section('breadcrumb', 'SIMOR / Stok / Bahan Baku / Edit')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="font-bold" style="font-size:1.25rem;color:#1e293b;">Edit Bahan Baku</h2>
    <a href="{{ route('ingredients.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card" style="max-width:720px;">
    <div class="card-header">
        <h3>{{ $ingredient->name }}</h3>
        <span class="badge badge-info">Stok saat ini: {{ $ingredient->current_stock }} {{ $ingredient->unit }}</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('ingredients.update', $ingredient) }}">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="form-group" style="grid-column:span 2;">
                    <label class="form-label">Nama Bahan Baku <span style="color:#ef4444">*</span></label>
                    <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name', $ingredient->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori <span style="color:#ef4444">*</span></label>
                    <select name="category_id" class="form-control" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $ingredient->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Satuan <span style="color:#ef4444">*</span></label>
                    <select name="unit" class="form-control" required>
                        @foreach(['kg','gram','liter','ml','butir','botol','pack','pcs','karton','sachet'] as $u)
                            <option value="{{ $u }}" {{ old('unit', $ingredient->unit) === $u ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Minimum Threshold <span style="color:#ef4444">*</span></label>
                    <input type="number" name="min_stock_threshold" class="form-control" step="0.01" min="0"
                           value="{{ old('min_stock_threshold', $ingredient->min_stock_threshold) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Harga Satuan (Rp)</label>
                    <input type="number" name="unit_price" class="form-control" min="0"
                           value="{{ old('unit_price', $ingredient->unit_price) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Lokasi Penyimpanan</label>
                    <select name="storage_location" class="form-control">
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach(['Freezer A','Freezer B','Chiller B','Chiller C','Dry Storage'] as $loc)
                            <option value="{{ $loc }}" {{ old('storage_location', $ingredient->storage_location) === $loc ? 'selected' : '' }}>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Kadaluarsa</label>
                    <input type="date" name="expiry_date" class="form-control"
                           value="{{ old('expiry_date', $ingredient->expiry_date?->toDateString()) }}">
                </div>
            </div>

            <div class="flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Update
                </button>
                <a href="{{ route('ingredients.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
