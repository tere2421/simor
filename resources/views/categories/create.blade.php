@extends('layouts.app')
@section('title','Tambah Kategori')
@section('breadcrumb','SIMOR / Stok / Kategori / Tambah')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="font-bold" style="font-size:1.25rem;">Tambah Kategori</h2>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>
<div class="card" style="max-width:560px;">
    <div class="card-header"><h3>Form Kategori Baru</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Kategori <span style="color:#ef4444">*</span></label>
                <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                       value="{{ old('name') }}" placeholder="Contoh: Daging & Protein" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi singkat kategori...">{{ old('description') }}</textarea>
            </div>
            <div class="flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
