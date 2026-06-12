@extends('layouts.app')
@section('title', 'Jadwal Mingguan')
@section('breadcrumb', 'SIMOR / Penjadwalan / Jadwal')

@section('content')

<style>
.sched-table { width:100%; border-collapse:collapse; min-width:900px; }
.sched-table th {
    padding:.65rem .75rem; text-align:center;
    font-size:.72rem; font-weight:700; text-transform:uppercase;
    letter-spacing:.05em; color:#64748b; background:#f8fafc;
    border-bottom:2px solid #e2e8f0; white-space:nowrap;
}
.sched-table th.staff-col { text-align:left; min-width:170px; }
.sched-table td {
    padding:.5rem .5rem; border-bottom:1px solid #f1f5f9;
    border-right:1px solid #f1f5f9; text-align:center;
    vertical-align:middle;
}
.sched-table td.staff-cell {
    text-align:left; padding:.6rem .85rem;
    background:#fafafa; border-right:2px solid #e2e8f0;
}
.sched-table tr:hover td { background:#f8fbff; }
.sched-table tr:hover td.staff-cell { background:#f0f7ff; }

/* day header today */
.today-col { background:#eff6ff !important; color:#1e40af !important; }

/* shift select */
.shift-select {
    width:100%; padding:.4rem .5rem;
    border:1.5px solid #e2e8f0; border-radius:.4rem;
    font-size:.76rem; font-weight:600; color:#334155;
    background:#fff; cursor:pointer;
    transition:border-color .15s, box-shadow .15s;
    appearance:none; text-align:center;
}
.shift-select:focus { outline:none; border-color:#3b82f6; box-shadow:0 0 0 2px rgba(59,130,246,.15); }
.shift-select.has-pagi  { background:#eff6ff; border-color:#bfdbfe; color:#1e40af; }
.shift-select.has-siang { background:#fef9c3; border-color:#fde68a; color:#854d0e; }
.shift-select.has-malam { background:#f5f3ff; border-color:#ddd6fe; color:#5b21b6; }
.shift-select.has-off   { background:#f1f5f9; border-color:#e2e8f0; color:#94a3b8; }

/* export bar */
.export-bar {
    display:flex; align-items:center; gap:.75rem; flex-wrap:wrap;
    background:#fff; border:1px solid #e2e8f0; border-radius:.75rem;
    padding:1rem 1.25rem; margin-bottom:1.25rem;
}
.export-badge {
    background:#f0fdf4; border:1px solid #86efac; color:#166534;
    font-size:.78rem; font-weight:700; padding:.35rem .9rem; border-radius:9999px;
    display:flex; align-items:center; gap:.4rem;
}
</style>

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="font-bold" style="font-size:1.25rem;color:#1e293b;">Jadwal Mingguan Staff</h2>
        <p class="text-sm text-muted">Atur shift langsung dari tabel — klik dropdown, lalu simpan</p>
    </div>
    <div class="flex gap-2">
        @if($pendingCount > 0)
        <span class="badge badge-warning" style="font-size:.8rem;padding:.4rem .9rem;">
            <i class="fa-solid fa-clock"></i> {{ $pendingCount }} belum disimpan
        </span>
        @endif
    </div>
</div>

{{-- Week Navigation --}}
<div class="card mb-4">
    <div class="card-body" style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
        @php
            [$yr, $wk] = explode('-W', $week);
            $prevWeek = \Carbon\Carbon::now()->setISODate((int)$yr, (int)$wk)->subWeek()->format('Y-\WW');
            $nextWeek = \Carbon\Carbon::now()->setISODate((int)$yr, (int)$wk)->addWeek()->format('Y-\WW');
        @endphp
        <a href="{{ route('schedules.index', ['week' => $prevWeek]) }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-chevron-left"></i>
        </a>
        <input type="week" id="weekPicker" class="form-control" style="width:auto;"
               value="{{ $week }}"
               onchange="window.location='{{ route('schedules.index') }}?week='+this.value">
        <a href="{{ route('schedules.index', ['week' => $nextWeek]) }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-chevron-right"></i>
        </a>
        <span class="font-semibold text-sm" style="color:#64748b;">
            <i class="fa-regular fa-calendar"></i>
            {{ $dates->first()->locale('id')->isoFormat('D MMM') }} –
            {{ $dates->last()->locale('id')->isoFormat('D MMM YYYY') }}
        </span>
        <a href="{{ route('schedules.index') }}" class="btn btn-secondary btn-sm" style="margin-left:auto;">
            <i class="fa-solid fa-rotate"></i> Minggu Ini
        </a>
    </div>
</div>

{{-- Export Bar (muncul setelah save) --}}
@if($hasSaved)
<div class="export-bar" id="exportBar">
    <div class="export-badge">
        <i class="fa-solid fa-circle-check"></i>
        Jadwal minggu ini tersimpan
    </div>
    <span class="text-sm text-muted" style="flex:1;">Ekspor jadwal ke format:</span>
    <form method="GET" action="{{ route('schedules.export') }}" style="display:flex;gap:.5rem;align-items:center;">
        <input type="hidden" name="week" value="{{ $week }}">
        <select name="format" class="form-control" style="width:auto;padding:.45rem .75rem;font-size:.83rem;">
            <option value="excel">Excel (.xlsx)</option>
            <option value="csv">CSV (.csv)</option>
            <option value="pdf">PDF (.pdf)</option>
        </select>
        <button type="submit" class="btn btn-success btn-sm">
            <i class="fa-solid fa-file-export"></i> Ekspor
        </button>
    </form>
</div>
@endif

{{-- Schedule Grid Form --}}
<form method="POST" action="{{ route('schedules.bulkSave') }}" id="schedForm">
    @csrf
    <input type="hidden" name="week" value="{{ $week }}">

    <div class="card mb-4">
        <div style="overflow-x:auto;">
            <table class="sched-table">
                <thead>
                    <tr>
                        <th class="staff-col">Staff</th>
                        @foreach($dates as $date)
                            <th class="{{ $date->isToday() ? 'today-col' : '' }}" style="min-width:120px;">
                                <div style="font-size:.85rem;font-weight:800;">
                                    {{ $date->locale('id')->isoFormat('ddd') }}
                                </div>
                                <div style="font-size:1rem;font-weight:900;">{{ $date->format('d') }}</div>
                                <div style="font-size:.68rem;font-weight:500;opacity:.7;">{{ $date->format('M') }}</div>
                            </th>
                        @endforeach
                        <th style="min-width:90px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staff as $s)
                    <tr>
                        <td class="staff-cell">
                            <div style="display:flex;align-items:center;gap:.6rem;">
                                <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#1e40af);display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:800;color:#fff;flex-shrink:0;">
                                    {{ strtoupper(substr($s->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-semibold" style="font-size:.8rem;color:#1e293b;line-height:1.2;">{{ $s->name }}</div>
                                    <div style="font-size:.65rem;color:#94a3b8;">{{ $s->position }}</div>
                                    <span style="font-size:.6rem;font-weight:700;padding:1px 5px;border-radius:3px;
                                        background:{{ $s->shift_type==='FT' ? '#dbeafe' : '#f1f5f9' }};
                                        color:{{ $s->shift_type==='FT' ? '#1e40af' : '#64748b' }};">
                                        {{ $s->shift_type }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        @php $weekShifts = 0; @endphp
                        @foreach($dates as $date)
                            @php
                                $dateStr  = $date->toDateString();
                                $existing = $schedules[$s->id][$dateStr][0] ?? null;
                                $val      = $existing ? $existing->shift_id : '';
                                if ($val) $weekShifts++;
                            @endphp
                            <td style="{{ $date->isToday() ? 'background:#f8fbff;' : '' }}">
                                <select
                                    name="schedules[{{ $s->id }}][{{ $dateStr }}]"
                                    class="shift-select {{ $val ? 'has-'.strtolower(explode(' ', $shifts->find($val)?->name ?? '')[1] ?? '') : '' }}"
                                    onchange="colorSelect(this)"
                                    data-staff="{{ $s->id }}"
                                    data-date="{{ $dateStr }}">
                                    <option value="">— Libur —</option>
                                    @foreach($shifts as $sh)
                                        <option value="{{ $sh->id }}"
                                            data-color="{{ $sh->color ?? '' }}"
                                            {{ $val == $sh->id ? 'selected' : '' }}>
                                            {{ $sh->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        @endforeach

                        <td>
                            <span class="badge {{ $weekShifts >= 5 ? 'badge-success' : ($weekShifts >= 3 ? 'badge-warning' : 'badge-gray') }}"
                                  id="total-{{ $s->id }}">
                                {{ $weekShifts }}x
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Footer actions --}}
        <div class="flex gap-2">
            <button type="button" class="btn btn-secondary btn-sm" onclick="clearAll()">
                <i class="fa-solid fa-eraser"></i> Bersihkan
            </button>
            <button type="submit" class="btn btn-primary" id="saveBtn">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Jadwal Mingguan
            </button>
        </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
// Color select based on value
function colorSelect(sel) {
    sel.className = 'shift-select';
    const opt = sel.options[sel.selectedIndex];
    const text = opt.text.toLowerCase();
    if (!sel.value) { sel.classList.add('has-off'); }
    else if (text.includes('pagi'))  sel.classList.add('has-pagi');
    else if (text.includes('siang')) sel.classList.add('has-siang');
    else if (text.includes('malam')) sel.classList.add('has-malam');

    // Update weekly count
    const staffId = sel.dataset.staff;
    const row  = document.querySelectorAll(`[data-staff="${staffId}"]`);
    const cnt  = [...row].filter(s => s.value).length;
    const badge = document.getElementById('total-' + staffId);
    if (badge) {
        badge.textContent = cnt + 'x';
        badge.className = 'badge ' + (cnt >= 5 ? 'badge-success' : cnt >= 3 ? 'badge-warning' : 'badge-gray');
    }
}

function clearAll() {
    if (!confirm('Kosongkan semua jadwal minggu ini?')) return;
    document.querySelectorAll('.shift-select').forEach(s => {
        s.value = '';
        s.className = 'shift-select has-off';
    });
}

// Init colors on load
document.querySelectorAll('.shift-select').forEach(s => colorSelect(s));

// Save feedback
document.getElementById('schedForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('saveBtn');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
    btn.disabled = true;
});
</script>
@endpush
@endsection
