@extends('layouts.app')
@section('title','Catat Kendala Kehadiran')
@section('breadcrumb','SIMOR / Penjadwalan / Kendala Kehadiran / Catat')
@section('content')

<div class="flex items-center justify-between mb-6">
    <h2 class="font-bold" style="font-size:1.25rem;">Catat Kendala Kehadiran</h2>
    <a href="{{ route('attendances.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="grid gap-4" style="grid-template-columns:1fr 320px;align-items:start;">
    <div class="card">
        <div class="card-header"><h3>Form Kendala</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('attendances.store') }}" id="attForm">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Staff <span style="color:#ef4444">*</span></label>
                        <select name="staff_profile_id" class="form-control" required
                                onchange="loadSchedule(this.value)">
                            <option value="">-- Pilih Staff --</option>
                            @foreach($staff as $s)
                                <option value="{{ $s->id }}"
                                    {{ old('staff_profile_id') == $s->id ? 'selected':'' }}>
                                    {{ $s->name }} ({{ $s->position }})
                                </option>
                            @endforeach
                        </select>
                        @error('staff_profile_id')<div class="invalid-feedback" style="display:block">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal <span style="color:#ef4444">*</span></label>
                        <input type="date" name="date" class="form-control"
                               value="{{ old('date', today()->toDateString()) }}" required>
                    </div>
                </div>

                {{-- Kategori Kendala --}}
                <div class="form-group">
                    <label class="form-label">Kategori Kendala <span style="color:#ef4444">*</span></label>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.6rem;">
                        @foreach([
                            ['terlambat',   '⏰','Terlambat',   '#f59e0b','#fffbeb'],
                            ['pulang_awal', '🏃','Pulang Awal', '#f59e0b','#fffbeb'],
                            ['sakit',       '🤒','Sakit',       '#3b82f6','#eff6ff'],
                            ['izin',        '📋','Izin',        '#3b82f6','#eff6ff'],
                            ['alpha',       '❌','Alpha',       '#ef4444','#fef2f2'],
                            ['tidak_hadir', '🚫','Tidak Hadir', '#ef4444','#fef2f2'],
                            ['masalah_lain','⚠️','Masalah Lain','#8b5cf6','#f5f3ff'],
                        ] as [$val,$icon,$label,$clr,$bg])
                        <label style="cursor:pointer;">
                            <input type="radio" name="status" value="{{ $val }}"
                                   {{ old('status') === $val ? 'checked':'' }}
                                   style="display:none;" onchange="toggleStatusFields('{{ $val }}')">
                            <div id="btn-{{ $val }}"
                                 style="border:2px solid #e2e8f0;background:#f8fafc;border-radius:.5rem;
                                        padding:.65rem .75rem;text-align:center;transition:all .15s;cursor:pointer;"
                                 onclick="selectStatus('{{ $val }}','{{ $clr }}','{{ $bg }}')">
                                <div style="font-size:1.1rem;">{{ $icon }}</div>
                                <div style="font-size:.75rem;font-weight:700;color:#475569;margin-top:2px;">{{ $label }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('status')<div class="invalid-feedback" style="display:block;margin-top:.5rem;">{{ $message }}</div>@enderror
                </div>

                {{-- Fields khusus Terlambat --}}
                <div id="fields-terlambat" style="display:none;background:#fffbeb;border:1px solid #fde68a;border-radius:.5rem;padding:1rem;margin-bottom:1.25rem;">
                    <div class="font-semibold text-sm" style="color:#854d0e;margin-bottom:.75rem;">
                        <i class="fa-solid fa-clock"></i> Detail Keterlambatan
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Jam Masuk Aktual</label>
                            <input type="time" name="check_in_actual" class="form-control"
                                   value="{{ old('check_in_actual') }}">
                            @error('check_in_actual')<div class="invalid-feedback" style="display:block">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Menit Terlambat</label>
                            <input type="number" name="late_minutes" class="form-control" min="1"
                                   value="{{ old('late_minutes') }}" placeholder="Otomatis jika ada jadwal">
                        </div>
                    </div>
                </div>

                {{-- Fields khusus Pulang Awal --}}
                <div id="fields-pulang_awal" style="display:none;background:#fffbeb;border:1px solid #fde68a;border-radius:.5rem;padding:1rem;margin-bottom:1.25rem;">
                    <div class="font-semibold text-sm" style="color:#854d0e;margin-bottom:.75rem;">
                        <i class="fa-solid fa-person-walking-arrow-right"></i> Detail Pulang Awal
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label class="form-label">Jam Pulang Aktual</label>
                        <input type="time" name="check_out" class="form-control"
                               value="{{ old('check_out') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Keterangan / Alasan</label>
                    <input type="text" name="problem_description" class="form-control"
                           value="{{ old('problem_description') }}"
                           placeholder="Contoh: Macet di Pluit, surat dokter, izin keluarga...">
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan Tambahan</label>
                    <textarea name="notes" class="form-control" rows="2"
                              placeholder="Catatan internal (opsional)">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary w-full" style="justify-content:center;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Kendala
                </button>
            </form>
        </div>
    </div>

    {{-- Panduan --}}
    <div class="card" style="background:linear-gradient(135deg,#0f2035,#1a3350);">
        <div class="card-body">
            <h3 style="color:#fff;margin-bottom:1rem;font-size:.95rem;">
                <i class="fa-solid fa-circle-info" style="color:#60a5fa;"></i> Panduan Pencatatan
            </h3>
            @foreach([
                ['⏰','Terlambat','Staff masuk melebihi jam jadwal shift. Isi jam masuk aktual.'],
                ['🏃','Pulang Awal','Staff pulang sebelum jam selesai shift. Isi jam pulang aktual.'],
                ['🤒','Sakit','Lampirkan catatan/surat sakit jika ada.'],
                ['📋','Izin','Izin terencana yang sudah disetujui SM/PIC.'],
                ['❌','Alpha','Tidak hadir tanpa keterangan (tanpa konfirmasi).'],
                ['🚫','Tidak Hadir','Tidak hadir dengan alasan yang diketahui.'],
                ['⚠️','Masalah Lain','Kendala operasional lain yang perlu dicatat.'],
            ] as [$ico,$lbl,$desc])
            <div style="display:flex;gap:.6rem;margin-bottom:.75rem;">
                <span style="font-size:.9rem;flex-shrink:0;">{{ $ico }}</span>
                <div>
                    <div style="color:#fff;font-size:.8rem;font-weight:700;">{{ $lbl }}</div>
                    <div style="color:#64748b;font-size:.72rem;line-height:1.5;">{{ $desc }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
const statusColors = {
    terlambat:   ['#f59e0b','#fffbeb'],
    pulang_awal: ['#f59e0b','#fffbeb'],
    sakit:       ['#3b82f6','#eff6ff'],
    izin:        ['#3b82f6','#eff6ff'],
    alpha:       ['#ef4444','#fef2f2'],
    tidak_hadir: ['#ef4444','#fef2f2'],
    masalah_lain:['#8b5cf6','#f5f3ff'],
};

function selectStatus(val, clr, bg) {
    // Reset semua tombol
    Object.keys(statusColors).forEach(k => {
        const btn = document.getElementById('btn-'+k);
        if (btn) { btn.style.border='2px solid #e2e8f0'; btn.style.background='#f8fafc'; }
        const radio = document.querySelector(`[name=status][value="${k}"]`);
        if (radio) radio.checked = false;
    });
    // Set aktif
    const btn = document.getElementById('btn-'+val);
    if (btn) { btn.style.border=`2px solid ${clr}`; btn.style.background=bg; }
    const radio = document.querySelector(`[name=status][value="${val}"]`);
    if (radio) radio.checked = true;
    toggleStatusFields(val);
}

function toggleStatusFields(val) {
    document.getElementById('fields-terlambat').style.display   = val==='terlambat'   ? '' : 'none';
    document.getElementById('fields-pulang_awal').style.display = val==='pulang_awal' ? '' : 'none';
}

// Init jika ada old value
const oldStatus = '{{ old("status") }}';
if (oldStatus) {
    const [clr, bg] = statusColors[oldStatus] || ['#64748b','#f1f5f9'];
    selectStatus(oldStatus, clr, bg);
}
</script>
@endpush
@endsection
