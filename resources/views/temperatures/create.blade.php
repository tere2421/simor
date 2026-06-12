@extends('layouts.app')
@section('title','Catat Suhu')
@section('breadcrumb','SIMOR / Monitoring / Suhu / Catat')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="font-bold" style="font-size:1.25rem;">Catat Suhu Penyimpanan</h2>
    <a href="{{ route('temperatures.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>
<div class="card" style="max-width:560px;">
    <div class="card-header"><h3>Form Pencatatan Suhu</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('temperatures.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Zona Penyimpanan <span style="color:#ef4444">*</span></label>
                <select name="zone_id" class="form-control" required onchange="showRange(this)">
                    <option value="">-- Pilih Zona --</option>
                    @foreach($zones as $z)
                        <option value="{{ $z->id }}" data-min="{{ $z->min_temp }}" data-max="{{ $z->max_temp }}">
                            {{ $z->name }} — {{ $z->location }} ({{ $z->min_temp }}° ~ {{ $z->max_temp }}°C)
                        </option>
                    @endforeach
                </select>
                <div id="rangeInfo" style="display:none;margin-top:.5rem;font-size:.8rem;color:#3b82f6;">
                    <i class="fa-solid fa-info-circle"></i> Range normal: <strong id="rangeText"></strong>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Suhu (°C) <span style="color:#ef4444">*</span></label>
                <input type="number" name="temperature" class="form-control" step="0.1" placeholder="Contoh: -20.5"
                       value="{{ old('temperature') }}" required onchange="previewStatus(this.value)">
                <div id="tempPreview" style="margin-top:.5rem;display:none;" class="text-sm font-semibold"></div>
            </div>
            <div class="form-group">
                <label class="form-label">Waktu Pencatatan</label>
                <input type="datetime-local" name="recorded_at" class="form-control"
                       value="{{ old('recorded_at', now()->format('Y-m-d\TH:i')) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Opsional...">{{ old('notes') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary w-full"><i class="fa-solid fa-floppy-disk"></i> Simpan Data Suhu</button>
        </form>
    </div>
</div>
@push('scripts')
<script>
let minT = null, maxT = null;
function showRange(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (!opt.value) { document.getElementById('rangeInfo').style.display='none'; return; }
    minT = parseFloat(opt.dataset.min); maxT = parseFloat(opt.dataset.max);
    document.getElementById('rangeText').textContent = minT + '°C ~ ' + maxT + '°C';
    document.getElementById('rangeInfo').style.display = 'block';
    previewStatus(document.querySelector('[name=temperature]').value);
}
function previewStatus(val) {
    const el = document.getElementById('tempPreview');
    if (!val || minT === null) { el.style.display='none'; return; }
    const t = parseFloat(val);
    const ok = t >= minT && t <= maxT;
    el.style.display = 'block';
    el.style.color = ok ? '#22c55e' : '#ef4444';
    el.textContent = ok ? '✓ Suhu NORMAL' : '⚠ Suhu ABNORMAL — akan tercatat sebagai alert';
}
</script>
@endpush
@endsection
