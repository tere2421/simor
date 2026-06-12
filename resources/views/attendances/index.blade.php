@extends('layouts.app')
@section('title','Kendala Kehadiran')
@section('breadcrumb','SIMOR / Penjadwalan / Kendala Kehadiran')

@section('content')
@php
$statusConfig = [
    'terlambat'    => ['badge-warning','⏰','Terlambat'],
    'alpha'        => ['badge-danger', '❌','Alpha'],
    'tidak_hadir'  => ['badge-danger', '🚫','Tidak Hadir'],
    'izin'         => ['badge-info',   '📋','Izin'],
    'sakit'        => ['badge-warning','🤒','Sakit'],
    'pulang_awal'  => ['badge-warning','🏃','Pulang Awal'],
    'masalah_lain' => ['badge-danger', '⚠️','Masalah Lain'],
];
@endphp

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="font-bold" style="font-size:1.25rem;">Kendala & Ketidakhadiran Staff</h2>
        <p class="text-sm text-muted">Catat terlambat, izin, sakit, alpha, dan kendala lainnya</p>
    </div>
    @if(auth()->user()->isManager())
    <a href="{{ route('attendances.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-triangle-exclamation"></i> Catat Kendala
    </a>
    @endif
</div>

{{-- Filter --}}
<div class="card mb-4">
    <form method="GET" class="card-body" style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;">
        <div>
            <label class="form-label">Tanggal Spesifik</label>
            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div>
            <label class="form-label">Atau Bulan</label>
            <input type="month" name="month" class="form-control" value="{{ request('month', now()->format('Y-m')) }}">
        </div>
        <div>
            <label class="form-label">Kategori</label>
            <select name="status" class="form-control">
                <option value="">Semua</option>
                @foreach($statusConfig as $val => [$badge, $icon, $label])
                    <option value="{{ $val }}" {{ request('status')===$val ? 'selected':'' }}>
                        {{ $icon }} {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        <a href="{{ route('attendances.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid gap-4 mb-6" style="grid-template-columns:repeat(4,1fr);">
    @foreach([
        ['terlambat',  '#f59e0b','#fffbeb','fa-clock'],
        ['alpha',      '#ef4444','#fef2f2','fa-circle-xmark'],
        ['tidak_hadir','#ef4444','#fef2f2','fa-user-slash'],
        ['sakit',      '#3b82f6','#eff6ff','fa-hospital'],
    ] as [$st,$clr,$bg,$ico])
    <div class="stat-card" style="border-left-color:{{ $clr }};">
        <div class="stat-icon" style="background:{{ $bg }};color:{{ $clr }};">
            <i class="fa-solid {{ $ico }}"></i>
        </div>
        <div>
            <div class="stat-value" style="color:{{ $clr }};">{{ $summary[$st] ?? 0 }}</div>
            <div class="stat-label">{{ $statusConfig[$st][2] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Table --}}
<div class="card">
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Staff</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Detail Kendala</th>
                    <th>Dicatat Oleh</th>   {{-- ← kolom yang difix --}}
                    <th>Waktu Catat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $att)
                @php [$badge, $icon, $label] = $statusConfig[$att->status] ?? ['badge-gray','❓',$att->status]; @endphp
                <tr>
                    <td>
                        <div class="font-semibold">{{ $att->staffProfile->name }}</div>
                        <div class="text-xs text-muted">{{ $att->staffProfile->position }}</div>
                    </td>
                    <td>
                        <div class="font-semibold text-sm">
                            {{ $att->date->setTimezone('Asia/Jakarta')->locale('id')->isoFormat('ddd, D MMM YYYY') }}
                        </div>
                        @if($att->schedule)
                            <div class="text-xs text-muted">
                                Jadwal: {{ $att->schedule->shift->code ?? $att->schedule->shift->name }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $badge }}" style="font-size:.82rem;">
                            {{ $icon }} {{ $label }}
                        </span>
                    </td>
                    <td style="max-width:260px;">
                        @if($att->status === 'terlambat' && $att->check_in_actual)
                            <div class="text-sm font-semibold" style="color:#f59e0b;">
                                Masuk: {{ $att->check_in_actual }}
                                @if($att->late_minutes)
                                    <span class="badge badge-warning" style="margin-left:4px;">
                                        +{{ $att->late_minutes }} menit
                                    </span>
                                @endif
                            </div>
                        @endif
                        @if($att->status === 'pulang_awal' && $att->check_out)
                            <div class="text-sm" style="color:#f59e0b;">
                                Pulang jam: {{ $att->check_out }}
                            </div>
                        @endif
                        @if($att->problem_description)
                            <div class="text-xs" style="color:#475569;margin-top:2px;">
                                {{ $att->problem_description }}
                            </div>
                        @endif
                        @if($att->notes)
                            <div class="text-xs text-muted">{{ $att->notes }}</div>
                        @endif
                    </td>

                    {{-- ── DICATAT OLEH — pakai recorder(), bukan login user ── --}}
                    <td>
                        @if($att->recorder)
                            <div class="font-semibold text-sm">{{ $att->recorder->name }}</div>
                            <span class="badge {{
                                $att->recorder->role === 'SM'  ? 'badge-warning' :
                                ($att->recorder->role === 'PIC' ? 'badge-info' : 'badge-gray')
                            }}" style="font-size:.62rem;">
                                {{ $att->recorder->role }}
                            </span>
                        @else
                            <span class="text-muted text-xs">—</span>
                        @endif
                    </td>

                    <td class="text-xs text-muted">
                        {{ $att->created_at->setTimezone('Asia/Jakarta')->locale('id')->isoFormat('D MMM, HH:mm') }}
                    </td>

                    <td>
                        @if(auth()->user()->isManager())
                        <form method="POST" action="{{ route('attendances.destroy', $att) }}"
                              onsubmit="return confirm('Hapus catatan ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted" style="padding:3rem;">
                        <i class="fa-solid fa-party-horn fa-2x" style="display:block;margin-bottom:.75rem;opacity:.3;"></i>
                        Tidak ada kendala kehadiran untuk periode ini 🎉
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body" style="border-top:1px solid #f1f5f9;">
        {{ $records->links() }}
    </div>
</div>
@endsection
