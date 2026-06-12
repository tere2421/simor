@extends('layouts.app')
@section('title','Stock Opname')
@section('breadcrumb','SIMOR / Stok / Stock Opname')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="font-bold" style="font-size:1.25rem;">Stock Opname Bulanan</h2>
        <p class="text-sm text-muted">Penyesuaian stok fisik vs sistem setiap akhir bulan</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('opnames.download-template') }}" class="btn btn-secondary">
            <i class="fa-solid fa-file-arrow-down"></i> Download Template CSV
        </a>
        <a href="{{ route('opnames.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Opname Baru
        </a>
    </div>
</div>

<div class="card">
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Kode Opname</th><th>Periode</th><th>Tanggal</th>
                    <th>Item</th><th>Status</th><th>Dibuat Oleh</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($opnames as $op)
                <tr>
                    <td style="font-family:monospace;font-weight:700;color:#1e40af;">{{ $op->opname_code }}</td>
                    <td><span class="badge badge-info">{{ $op->period }}</span></td>
                    <td class="text-sm">{{ $op->opname_date->format('d/m/Y') }}</td>
                    <td><span class="badge badge-gray">{{ $op->lines()->count() }} item</span></td>
                    <td>
                        <span class="badge {{
                            $op->status === 'approved' ? 'badge-success' :
                            ($op->status === 'submitted' ? 'badge-warning' : 'badge-gray')
                        }}">
                            {{ strtoupper($op->status) }}
                        </span>
                    </td>
                    <td class="text-xs">{{ $op->user->name }}</td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('opnames.show', $op) }}" class="btn btn-sm btn-secondary">
                                <i class="fa-solid fa-eye"></i> Detail
                            </a>
                            @if($op->status !== 'approved' && auth()->user()->isManager())
                                <form method="POST" action="{{ route('opnames.approve', $op) }}"
                                      onsubmit="return confirm('Approve & update semua stok sesuai hasil opname?')">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-success">
                                        <i class="fa-solid fa-check"></i> Approve
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted" style="padding:3rem;">
                    <i class="fa-solid fa-boxes-stacked fa-2x" style="display:block;margin-bottom:.75rem;opacity:.3;"></i>
                    Belum ada stock opname
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body" style="border-top:1px solid #f1f5f9;">{{ $opnames->links() }}</div>
</div>
@endsection
