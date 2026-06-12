{{-- FILE: categories/index.blade.php --}}
@extends('layouts.app')
@section('title','Kategori')
@section('breadcrumb','SIMOR / Stok / Kategori')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="font-bold" style="font-size:1.25rem;color:#1e293b;">Kategori Bahan Baku</h2>
    <a href="{{ route('categories.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Kategori</a>
</div>
<div class="card">
    <table>
        <thead><tr><th>#</th><th>Nama Kategori</th><th>Deskripsi</th><th>Jml Bahan Baku</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($categories as $i => $cat)
            <tr>
                <td class="text-muted text-xs">{{ $categories->firstItem() + $i }}</td>
                <td class="font-semibold">{{ $cat->name }}</td>
                <td class="text-muted text-sm">{{ $cat->description ?? '-' }}</td>
                <td><span class="badge badge-info">{{ $cat->ingredients_count }} item</span></td>
                <td>
                    <div class="flex gap-2">
                        <a href="{{ route('categories.edit', $cat) }}" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted" style="padding:2rem;">Belum ada kategori</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="card-body" style="border-top:1px solid #f1f5f9;">{{ $categories->links() }}</div>
</div>
@endsection
