@extends('layouts.app')
@section('title', 'Checklist Kebersihan')
@section('breadcrumb', 'SIMOR / Monitoring / Checklist')

@section('content')

@php
$shiftConfig = [
    'pagi'  => ['label' => 'Pagi',  'icon' => 'fa-sun',       'color' => '#f59e0b', 'bg' => '#fffbeb', 'border' => '#fde68a', 'time' => '07:00 – 15:00'],
    'siang' => ['label' => 'Siang', 'icon' => 'fa-cloud-sun', 'color' => '#3b82f6', 'bg' => '#eff6ff', 'border' => '#bfdbfe', 'time' => '12:00 – 20:00'],
    'malam' => ['label' => 'Malam', 'icon' => 'fa-moon',      'color' => '#8b5cf6', 'bg' => '#f5f3ff', 'border' => '#ddd6fe', 'time' => '16:00 – 22:00'],
];
$cfg = $shiftConfig[$session];
@endphp

<style>
.shift-tab {
    padding:.65rem 1.5rem; border-radius:.5rem; font-size:.88rem; font-weight:700;
    border:2px solid transparent; cursor:pointer; text-decoration:none;
    display:inline-flex; align-items:center; gap:.5rem; transition:all .15s;
}
.checklist-section {
    border-radius:.75rem; overflow:hidden;
    border:1.5px solid; margin-bottom:1rem;
}
.checklist-section-header {
    padding:.75rem 1.25rem; font-weight:700; font-size:.82rem;
    display:flex; align-items:center; gap:.6rem;
    text-transform:uppercase; letter-spacing:.06em;
}
.check-item {
    display:flex; align-items:flex-start; gap:1rem;
    padding:.875rem 1.25rem; border-top:1px solid;
    cursor:pointer; transition:background .12s;
}
.check-item:hover { filter:brightness(.97); }
.check-item input[type=checkbox] {
    width:18px; height:18px; margin-top:2px; flex-shrink:0;
    border-radius:4px; cursor:pointer;
}
</style>

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="font-bold" style="font-size:1.25rem;">Checklist Kebersihan & Operasional</h2>
        <p class="text-sm text-muted">
            {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
        </p>
    </div>
    <a href="{{ route('checklists.history') }}" class="btn btn-secondary">
        <i class="fa-solid fa-clock-rotate-left"></i> Riwayat
    </a>
</div>

{{-- Shift selector --}}
<div class="card mb-4">
    <div class="card-body" style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
        @foreach(['pagi','siang','malam'] as $s)
        @php $c = $shiftConfig[$s]; @endphp
        <a href="{{ route('checklists.index', ['date' => $date, 'session' => $s]) }}"
           class="shift-tab"
           style="{{ $session === $s
                ? "background:{$c['color']};color:#fff;border-color:{$c['color']};"
                : "background:{$c['bg']};color:{$c['color']};border-color:{$c['border']};" }}">
            <i class="fa-solid {{ $c['icon'] }}"></i>
            Shift {{ $c['label'] }}
            <span style="font-size:.7rem;opacity:.75;">{{ $c['time'] }}</span>
        </a>
        @endforeach

        <div style="margin-left:auto;display:flex;gap:.5rem;align-items:center;">
            <label class="form-label" style="margin:0;font-size:.8rem;">Tanggal:</label>
            <input type="date" class="form-control" style="width:auto;"
                   value="{{ $date }}"
                   onchange="window.location='{{ route('checklists.index') }}?session={{ $session }}&date='+this.value">
        </div>
    </div>
</div>

{{-- Progress bar --}}
@php $barColor = $donePct >= 80 ? '#22c55e' : ($donePct >= 50 ? '#f59e0b' : '#ef4444'); @endphp
<div style="background:#f1f5f9;border-radius:9999px;height:12px;margin-bottom:.75rem;overflow:hidden;">
    <div style="background:{{ $barColor }};height:100%;width:{{ $donePct }}%;transition:width .5s;border-radius:9999px;"></div>
</div>
<div class="flex justify-between text-xs text-muted mb-6">
    <span>Progress: <strong style="color:{{ $barColor }}">{{ $donePct }}%</strong> selesai</span>
    <span>{{ $records->filter()->count() }} / {{ $items->count() }} item</span>
</div>

<form method="POST" action="{{ route('checklists.store') }}">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">
    <input type="hidden" name="session" value="{{ $session }}">

    {{-- Section: Khusus shift ini --}}
    @php $shiftItems = $itemsByShift[$session] ?? collect(); @endphp
    @if($shiftItems->isNotEmpty())
    <div class="checklist-section" style="border-color:{{ $cfg['border'] }};">
        <div class="checklist-section-header"
             style="background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};border-bottom:1.5px solid {{ $cfg['border'] }};">
            <i class="fa-solid {{ $cfg['icon'] }}"></i>
            Checklist Khusus Shift {{ $cfg['label'] }}
            <span style="margin-left:auto;font-size:.72rem;font-weight:500;opacity:.7;">{{ $shiftItems->count() }} item</span>
        </div>
        @foreach($shiftItems as $item)
            @php $done = $records[$item->id] ?? false; @endphp
            <label class="check-item"
                   style="background:{{ $done ? $cfg['bg'] : '#fff' }};border-color:{{ $cfg['border'] }};"
                   onmouseover="this.style.background='{{ $cfg['bg'] }}'"
                   onmouseout="this.style.background='{{ $done ? $cfg['bg'] : '#fff' }}'">
                <input type="checkbox" name="items[]" value="{{ $item->id }}"
                       {{ $done ? 'checked' : '' }}
                       style="accent-color:{{ $cfg['color'] }};">
                <div style="flex:1;">
                    <div class="font-semibold" style="font-size:.875rem;color:#1e293b;{{ $done ? 'text-decoration:line-through;opacity:.5;' : '' }}">
                        {{ $item->order }}. {{ $item->name }}
                    </div>
                    @if($item->description)
                        <div class="text-xs text-muted" style="margin-top:2px;">{{ $item->description }}</div>
                    @endif
                </div>
                @if($done)
                    <i class="fa-solid fa-circle-check" style="color:{{ $cfg['color'] }};margin-top:3px;"></i>
                @endif
            </label>
        @endforeach
    </div>
    @endif

    {{-- Section: Berlaku semua shift --}}
    @php $allItems = $itemsByShift['all'] ?? collect(); @endphp
    @if($allItems->isNotEmpty())
    <div class="checklist-section" style="border-color:#e2e8f0;">
        <div class="checklist-section-header"
             style="background:#f8fafc;color:#64748b;border-bottom:1.5px solid #e2e8f0;">
            <i class="fa-solid fa-list-check"></i>
            Checklist Umum (Semua Shift)
            <span style="margin-left:auto;font-size:.72rem;font-weight:500;opacity:.7;">{{ $allItems->count() }} item</span>
        </div>
        @foreach($allItems as $item)
            @php $done = $records[$item->id] ?? false; @endphp
            <label class="check-item"
                   style="background:{{ $done ? '#f0fdf4' : '#fff' }};border-color:#f1f5f9;"
                   onmouseover="this.style.background='#f8fafc'"
                   onmouseout="this.style.background='{{ $done ? '#f0fdf4' : '#fff' }}'">
                <input type="checkbox" name="items[]" value="{{ $item->id }}"
                       {{ $done ? 'checked' : '' }}
                       style="accent-color:#22c55e;">
                <div style="flex:1;">
                    <div class="font-semibold" style="font-size:.875rem;color:#1e293b;{{ $done ? 'text-decoration:line-through;opacity:.5;' : '' }}">
                        {{ $item->order }}. {{ $item->name }}
                    </div>
                    @if($item->description)
                        <div class="text-xs text-muted" style="margin-top:2px;">{{ $item->description }}</div>
                    @endif
                </div>
                @if($done)
                    <i class="fa-solid fa-circle-check" style="color:#22c55e;margin-top:3px;"></i>
                @endif
            </label>
        @endforeach
    </div>
    @endif

    {{-- Action buttons --}}
    <div class="flex gap-2 justify-between" style="margin-top:1.25rem;">
        <button type="button" class="btn btn-secondary" onclick="toggleAll()">
            <i class="fa-solid fa-check-double"></i> Centang Semua
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            Simpan Checklist Shift {{ $cfg['label'] }}
        </button>
    </div>
</form>

{{-- Preview 3 shift progress --}}
<div class="grid grid-cols-3 gap-4 mt-6">
    @foreach(['pagi','siang','malam'] as $s)
    @php
        $c      = $shiftConfig[$s];
        $sItems = \App\Models\ChecklistItem::forShift($s)->count();
        $sDone  = \App\Models\ChecklistRecord::where('date', $date)
                    ->where('session', $s)->where('is_done', true)->count();
        $sPct   = $sItems > 0 ? round(($sDone / $sItems) * 100) : 0;
    @endphp
    <div class="card" style="border-top:3px solid {{ $c['color'] }};">
        <div class="card-body" style="text-align:center;padding:1.25rem;">
            <i class="fa-solid {{ $c['icon'] }}" style="font-size:1.5rem;color:{{ $c['color'] }};margin-bottom:.5rem;display:block;"></i>
            <div style="font-size:1.6rem;font-weight:900;color:{{ $c['color'] }};">{{ $sPct }}%</div>
            <div class="text-sm font-semibold" style="color:#1e293b;">Shift {{ $c['label'] }}</div>
            <div class="text-xs text-muted">{{ $sDone }}/{{ $sItems }} item</div>
            <div style="background:#f1f5f9;border-radius:9999px;height:6px;margin-top:.75rem;overflow:hidden;">
                <div style="background:{{ $c['color'] }};height:100%;width:{{ $sPct }}%;border-radius:9999px;"></div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@push('scripts')
<script>
let allChecked = false;
function toggleAll() {
    const boxes = document.querySelectorAll('[name="items[]"]');
    allChecked = !allChecked;
    boxes.forEach(b => b.checked = allChecked);
    document.querySelector('[onclick="toggleAll()"]').innerHTML =
        allChecked
        ? '<i class="fa-solid fa-xmark"></i> Hapus Semua'
        : '<i class="fa-solid fa-check-double"></i> Centang Semua';
}
</script>
@endpush
@endsection
