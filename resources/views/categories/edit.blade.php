@extends('layouts.app')
@section('title','Edit Kategori')
@section('breadcrumb','SIMOR / Stok / Kategori / Edit')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="font-bold" style="font-size:1.25rem;">Edit Kategori</h2>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>
<div class="card" style="max-width:560px;">
    <div class="card-header"><h3>{{ $category->name }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Kategori <span style="color:#ef4444">*</span></label>
                <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                       value="{{ old('name', $category->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea>
            </div>
            <div class="flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Update</button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
