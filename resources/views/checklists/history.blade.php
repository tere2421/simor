@extends('layouts.app')
@section('title','Riwayat Checklist')
@section('breadcrumb','SIMOR / Monitoring / Checklist / Riwayat')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="font-bold" style="font-size:1.25rem;">Riwayat Checklist</h2>
    <a href="{{ route('checklists.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>
<div class="card mb-4">
    <form method="GET" class="card-body" style="display:flex;gap:.75rem;align-items:flex-end;">
        <div>
            <label class="form-label">Tanggal</label>
            <input type="date" name="date" class="form-control" value="{{ request('date', today()->toDateString()) }}">
        </div>
        <button class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
    </form>
</div>
@foreach(['pagi','siang','malam'] as $sesi)
    <div class="card mb-4">
        <div class="card-header">
            <h3>Sesi {{ ucfirst($sesi) }}</h3>
            @if(isset($records[$sesi]))
                @php $done = $records[$sesi]->where('is_done', true)->count(); $total = $records[$sesi]->count(); @endphp
                <span class="badge {{ $done === $total ? 'badge-success' : ($done > 0 ? 'badge-warning' : 'badge-danger') }}">
                    {{ $done }}/{{ $total }} selesai
                </span>
            @else
                <span class="badge badge-gray">Belum diisi</span>
            @endif
        </div>
        @if(isset($records[$sesi]))
            <table>
                <thead><tr><th>Item</th><th>Status</th><th>Oleh</th></tr></thead>
                <tbody>
                    @foreach($records[$sesi] as $rec)
                    <tr>
                        <td>{{ $rec->item->name }}</td>
                        <td>
                            @if($rec->is_done)
                                <span class="badge badge-success"><i class="fa-solid fa-check"></i> Selesai</span>
                            @else
                                <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> Belum</span>
                            @endif
                        </td>
                        <td class="text-xs">{{ $rec->user->name ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="card-body text-muted text-center" style="padding:1.5rem;">Belum ada data untuk sesi ini</div>
        @endif
    </div>
@endforeach
@endsection
