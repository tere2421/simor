@extends('layouts.app')
@section('title','Kode Shift')
@section('breadcrumb','SIMOR / Penjadwalan / Kode Shift')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="font-bold" style="font-size:1.25rem;">Manajemen Kode Shift</h2>
        <p class="text-sm text-muted">Format: H + 2 digit durasi + 4 digit jam masuk. Contoh: <strong>H080800</strong></p>
    </div>
    @if(auth()->user()->isSM())
    <a href="{{ route('shifts.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Kode Shift
    </a>
    @endif
</div>

{{-- Format explanation --}}
<div class="card mb-4" style="background:linear-gradient(135deg,#0f2035,#1a3350);">
    <div class="card-body" style="display:flex;align-items:center;gap:2rem;flex-wrap:wrap;">
        <div style="text-align:center;">
            <div style="font-family:monospace;font-size:2.5rem;font-weight:900;color:#fff;letter-spacing:4px;">
                <span style="color:#fbbf24;">H</span><span style="color:#34d399;">08</span><span style="color:#60a5fa;">0800</span>
            </div>
            <div style="font-size:.72rem;color:#64748b;margin-top:4px;letter-spacing:2px;">KODE CONTOH</div>
        </div>
        <div style="display:flex;gap:2rem;flex-wrap:wrap;">
            <div style="text-align:center;">
                <div style="font-size:1.4rem;font-weight:800;color:#fbbf24;">H</div>
                <div style="font-size:.72rem;color:#94a3b8;">Kode<br>Perusahaan</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:1.4rem;font-weight:800;color:#34d399;">08</div>
                <div style="font-size:.72rem;color:#94a3b8;">Durasi<br>8 Jam</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:1.4rem;font-weight:800;color:#60a5fa;">0800</div>
                <div style="font-size:.72rem;color:#94a3b8;">Jam Masuk<br>08:00</div>
            </div>
            <div style="text-align:center;padding-left:1rem;border-left:1px solid rgba(255,255,255,.1);">
                <div style="font-size:1.4rem;font-weight:800;color:#f472b6;">16:00</div>
                <div style="font-size:.72rem;color:#94a3b8;">Jam Keluar<br>(Otomatis)</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table>
        <thead>
            <tr><th>Kode Shift</th><th>Durasi</th><th>Jam Masuk</th><th>Jam Keluar</th><th>Nama</th><th>Keterangan</th>
            @if(auth()->user()->isSM())<th>Aksi</th>@endif
            </tr>
        </thead>
        <tbody>
            @forelse($shifts as $shift)
            <tr>
                <td>
                    <span style="font-family:monospace;font-size:1.05rem;font-weight:800;
                        background:#f1f5f9;padding:4px 12px;border-radius:6px;
                        border:1.5px solid #e2e8f0;color:#1e293b;">
                        {{ $shift->code }}
                    </span>
                </td>
                <td>
                    <span class="badge badge-info">{{ $shift->duration_hours }} jam</span>
                </td>
                <td class="font-bold" style="color:#22c55e;font-size:1rem;">
                    {{ $shift->start_time }}
                </td>
                <td class="font-bold" style="color:#ef4444;font-size:1rem;">
                    {{ $shift->clock_out }}
                </td>
                <td class="text-sm text-muted">{{ $shift->name }}</td>
                <td class="text-xs text-muted">{{ $shift->description ?? '—' }}</td>
                @if(auth()->user()->isSM())
                <td>
                    <form method="POST" action="{{ route('shifts.destroy', $shift) }}"
                          onsubmit="return confirm('Hapus kode shift {{ $shift->code }}?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
                @endif
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted" style="padding:3rem;">
                Belum ada kode shift. Tambahkan kode shift terlebih dahulu.
            </td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="card-body" style="border-top:1px solid #f1f5f9;">{{ $shifts->links() }}</div>
</div>
@endsection
