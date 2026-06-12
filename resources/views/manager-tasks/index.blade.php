@extends('layouts.app')
@section('title', auth()->user()->isSM() ? 'Task SM & PIC' : 'Task PIC')
@section('breadcrumb', 'SIMOR / Checklist / Task Manajemen')

@section('content')
@php
$freqLabels = [
    'daily'=>'Setiap Hari','monday'=>'Senin','tuesday'=>'Selasa',
    'wednesday'=>'Rabu','thursday'=>'Kamis','friday'=>'Jumat',
    'weekly'=>'Mingguan','monthly'=>'Bulanan',
];
$freqColors = [
    'daily'    =>['#eff6ff','#3b82f6','#1e40af'],
    'monday'   =>['#f5f3ff','#8b5cf6','#5b21b6'],
    'tuesday'  =>['#fef9c3','#f59e0b','#854d0e'],
    'wednesday'=>['#f0fdf4','#22c55e','#166534'],
    'thursday' =>['#fff7ed','#f97316','#9a3412'],
    'friday'   =>['#fdf4ff','#c084fc','#7e22ce'],
    'weekly'   =>['#f0f9ff','#0ea5e9','#0c4a6e'],
    'monthly'  =>['#fff1f2','#fb7185','#881337'],
];
$todayFreq = strtolower(now()->locale('en')->dayName);
@endphp

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="font-bold" style="font-size:1.25rem;">
            @if(auth()->user()->isSM()) Task Checklist SM & PIC
            @else Task Checklist PIC @endif
        </h2>
        <p class="text-sm text-muted">
            Progress hari ini:
            <strong style="color:{{ $pct >= 80 ? '#22c55e' : ($pct >= 50 ? '#f59e0b' : '#ef4444') }}">
                {{ $pct }}%
            </strong>
            ({{ $done }}/{{ $total }} task)
        </p>
    </div>
    <div class="flex gap-2">
        @if(auth()->user()->isSM())
            <a href="{{ route('manager-tasks.manage') }}" class="btn btn-secondary">
                <i class="fa-solid fa-gear"></i> Kelola Task
            </a>
        @endif
        <input type="date" class="form-control" style="width:auto;"
               value="{{ $date }}"
               onchange="window.location='{{ route('manager-tasks.index') }}?date='+this.value">
    </div>
</div>

{{-- Progress bar --}}
@php $pctColor = $pct >= 80 ? '#22c55e' : ($pct >= 50 ? '#f59e0b' : '#ef4444'); @endphp
<div style="background:#f1f5f9;border-radius:9999px;height:10px;margin-bottom:1.5rem;overflow:hidden;">
    <div style="background:{{ $pctColor }};height:100%;width:{{ $pct }}%;transition:width .5s;border-radius:9999px;"></div>
</div>

<form method="POST" action="{{ route('manager-tasks.store') }}">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">

    @php
    // Tentukan sections yang ditampilkan
    $sections = [];
    if (auth()->user()->isSM()) {
        $sections = [
            ['title' => 'Task Store Manager (SM)', 'icon' => 'fa-user-tie', 'color' => '#f59e0b', 'bg' => '#fffbeb', 'grouped' => $groupedSM, 'roles' => ['SM','both']],
            ['title' => 'Task PIC',                'icon' => 'fa-user',     'color' => '#3b82f6', 'bg' => '#eff6ff', 'grouped' => $groupedPIC, 'roles' => ['PIC','both']],
        ];
    } else {
        $sections = [
            ['title' => 'Task PIC', 'icon' => 'fa-user', 'color' => '#3b82f6', 'bg' => '#eff6ff', 'grouped' => $groupedPIC, 'roles' => ['PIC','both']],
        ];
    }
    @endphp

    @foreach($sections as $section)
    <div style="margin-bottom:2rem;">
        {{-- Section header --}}
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
            <div style="width:36px;height:36px;border-radius:8px;background:{{ $section['bg'] }};
                        display:flex;align-items:center;justify-content:center;
                        color:{{ $section['color'] }};font-size:.95rem;">
                <i class="fa-solid {{ $section['icon'] }}"></i>
            </div>
            <h3 style="font-size:1rem;font-weight:800;color:#1e293b;">{{ $section['title'] }}</h3>
            @php
                $sectionTasks = $tasks->whereIn('role_target', $section['roles']);
                $sectionDone  = $sectionTasks->filter(fn($t) => $records[$t->id] ?? false)->count();
            @endphp
            <span class="badge badge-info" style="font-size:.72rem;">
                {{ $sectionDone }}/{{ $sectionTasks->count() }} selesai
            </span>
        </div>

        @forelse($section['grouped'] as $freq => $freqTasks)
        @php $c = $freqColors[$freq] ?? ['#f1f5f9','#64748b','#334155']; @endphp
        <div class="card mb-3" style="border-top:3px solid {{ $c[1] }};">
            <div class="card-header" style="background:{{ $c[0] }};border-bottom:1px solid {{ $c[1] }}30;">
                <div style="display:flex;align-items:center;gap:.6rem;">
                    <span style="background:{{ $c[1] }};color:#fff;padding:2px 10px;border-radius:4px;font-size:.72rem;font-weight:700;">
                        {{ $freqLabels[$freq] ?? $freq }}
                    </span>
                    @if(in_array($freq, ['daily', $todayFreq]))
                        <span style="background:#22c55e;color:#fff;padding:1px 7px;border-radius:3px;font-size:.62rem;font-weight:700;">
                            HARI INI
                        </span>
                    @endif
                </div>
                @php
                    $freqDone = $freqTasks->filter(fn($t) => $records[$t->id] ?? false)->count();
                @endphp
                <span style="font-size:.8rem;font-weight:700;color:{{ $c[2] }};">
                    {{ $freqDone }}/{{ $freqTasks->count() }}
                </span>
            </div>

            {{-- Group by category --}}
            @foreach($freqTasks->groupBy('category') as $cat => $catTasks)
                @if($cat)
                <div style="padding:.45rem 1.25rem .2rem;font-size:.68rem;font-weight:700;
                            text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;
                            background:#fafafa;border-bottom:1px solid #f1f5f9;">
                    {{ $cat }}
                </div>
                @endif

                @foreach($catTasks as $task)
                @php
                    $done     = $records[$task->id] ?? false;
                    $taskHist = $history[$task->id] ?? collect();
                @endphp
                <div style="border-bottom:1px solid #f8fafc;">
                    <label style="display:flex;align-items:flex-start;gap:1rem;padding:.8rem 1.25rem;
                                  cursor:pointer;background:{{ $done ? $c[0] : '#fff' }};transition:background .1s;"
                           onmouseover="this.style.background='{{ $c[0] }}'"
                           onmouseout="this.style.background='{{ $done ? $c[0] : '#fff' }}'">
                        <input type="checkbox" name="tasks[]" value="{{ $task->id }}"
                               {{ $done ? 'checked' : '' }}
                               style="width:17px;height:17px;accent-color:{{ $c[1] }};flex-shrink:0;margin-top:2px;cursor:pointer;">
                        <div style="flex:1;">
                            @if($task->url)
                                <a href="{{ $task->url }}" target="_blank"
                                   style="font-weight:600;font-size:.875rem;color:{{ $c[2] }};text-decoration:none;
                                          {{ $done ? 'text-decoration:line-through;opacity:.5;' : '' }}"
                                   onclick="event.stopPropagation()">
                                    {{ $task->title }}
                                    <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:.6rem;margin-left:3px;opacity:.6;"></i>
                                </a>
                            @else
                                <span style="font-weight:600;font-size:.875rem;color:#1e293b;
                                             {{ $done ? 'text-decoration:line-through;opacity:.5;' : '' }}">
                                    {{ $task->title }}
                                </span>
                            @endif

                            {{-- ── HISTORI PENCATATAN ── --}}
                            @if($taskHist->isNotEmpty())
                            <div style="margin-top:.45rem;display:flex;flex-wrap:wrap;gap:.35rem;">
                                @foreach($taskHist as $rec)
                                <div style="display:inline-flex;align-items:center;gap:.35rem;
                                            background:rgba(0,0,0,.05);border-radius:4px;
                                            padding:2px 8px;font-size:.68rem;color:#475569;">
                                    <i class="fa-solid fa-circle-check" style="color:#22c55e;font-size:.6rem;"></i>
                                    <span style="font-weight:600;">{{ $rec->user->name }}</span>
                                    <span style="color:#94a3b8;">·</span>
                                    <span>{{ $rec->updated_at->setTimezone('Asia/Jakarta')->locale('id')->isoFormat('D MMM, HH:mm') }}</span>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        @if($done)
                            <i class="fa-solid fa-circle-check" style="color:{{ $c[1] }};margin-top:3px;flex-shrink:0;"></i>
                        @endif
                    </label>
                </div>
                @endforeach
            @endforeach
        </div>
        @empty
            <div style="background:#f8fafc;border-radius:.5rem;padding:1.5rem;text-align:center;color:#94a3b8;font-size:.85rem;">
                Tidak ada task untuk section ini
            </div>
        @endforelse
    </div>
    @endforeach

    {{-- Sticky save button --}}
    <div style="position:sticky;bottom:0;background:#fff;border-top:2px solid #e2e8f0;
                padding:1rem 0;display:flex;justify-content:flex-end;gap:.75rem;">
        <button type="button" onclick="toggleAll()" class="btn btn-secondary">
            <i class="fa-solid fa-check-double"></i> Toggle Semua
        </button>
        <button type="submit" class="btn btn-primary" style="padding:.7rem 2rem;">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Progress
        </button>
    </div>
</form>

@push('scripts')
<script>
let all = false;
function toggleAll() {
    all = !all;
    document.querySelectorAll('[name="tasks[]"]').forEach(c => c.checked = all);
}
</script>
@endpush
@endsection
