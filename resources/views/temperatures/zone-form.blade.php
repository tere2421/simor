@extends('layouts.app')
@section('title', isset($zone) ? 'Edit Zona' : 'Tambah Zona')
@section('breadcrumb', 'SIMOR / Monitoring / Zona / '.(isset($zone) ? 'Edit' : 'Tambah'))

@section('content')
@php
$typeConfig = [
    'chiller'     => ['🧊','Chiller',      'Kulkas/chiller untuk menyimpan bahan yang perlu pendinginan (0–8°C)'],
    'freezer'     => ['❄️','Freezer',      'Freezer untuk protein beku dan WIP frozen (-18 s/d -25°C)'],
    'dry_storage' => ['📦','Dry Storage',  'Gudang kering untuk bahan non-perishable (15–30°C)'],
    'display'     => ['🏪','Display',      'Area display produk yang perlu monitoring suhu'],
    'other'       => ['🌡️','Lainnya',      'Tipe penyimpanan lainnya'],
];

// Range suhu default per tipe
$defaultRanges = [
    'chiller'     => ['min' => 0,   'max' => 4  ],
    'freezer'     => ['min' => -22, 'max' => -18 ],
    'dry_storage' => ['min' => 18,  'max' => 30  ],
    'display'     => ['min' => 2,   'max' => 8   ],
    'other'       => ['min' => 0,   'max' => 25  ],
];
@endphp

<div class="flex items-center justify-between mb-6">
    <h2 class="font-bold" style="font-size:1.25rem;">
        {{ isset($zone) ? 'Edit Zona: '.$zone->name : 'Tambah Zona Penyimpanan' }}
    </h2>
    <a href="{{ route('temperatures.zones') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="grid gap-4" style="grid-template-columns:1fr 320px;align-items:start;">

    <div class="card">
        <div class="card-header"><h3>Form Zona Penyimpanan</h3></div>
        <div class="card-body">
            <form method="POST"
                  action="{{ isset($zone)
                      ? route('temperatures.zones.update', $zone)
                      : route('temperatures.zones.store') }}">
                @csrf
                @if(isset($zone)) @method('PUT') @endif

                {{-- Tipe zona --}}
                <div class="form-group">
                    <label class="form-label">Tipe Equipment <span style="color:#ef4444">*</span></label>
                    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:.5rem;">
                        @foreach($typeConfig as $type => [$icon, $label, $desc])
                        @php $selected = old('type', $zone->type ?? '') === $type; @endphp
                        <label style="cursor:pointer;">
                            <input type="radio" name="type" value="{{ $type }}"
                                   {{ $selected ? 'checked' : '' }}
                                   style="display:none;"
                                   onchange="setDefaults('{{ $type }}')">
                            <div id="type-btn-{{ $type }}"
                                 onclick="selectType('{{ $type }}')"
                                 style="border:2px solid {{ $selected ? '#3b82f6' : '#e2e8f0' }};
                                        background:{{ $selected ? '#eff6ff' : '#f8fafc' }};
                                        border-radius:.5rem;padding:.6rem .4rem;text-align:center;
                                        transition:all .15s;cursor:pointer;">
                                <div style="font-size:1.2rem;">{{ $icon }}</div>
                                <div style="font-size:.7rem;font-weight:700;color:#475569;margin-top:2px;line-height:1.2;">
                                    {{ $label }}
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('type')<div class="invalid-feedback" style="display:block">{{ $message }}</div>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Nama Zona <span style="color:#ef4444">*</span></label>
                        <input type="text" name="name"
                               class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                               value="{{ old('name', $zone->name ?? '') }}"
                               placeholder="Contoh: Chiller A, Freezer 1, Dry Storage Utama"
                               required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="location"
                               class="form-control"
                               value="{{ old('location', $zone->location ?? '') }}"
                               placeholder="Contoh: Dapur Utama, Gudang Belakang">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Suhu Minimum (°C) <span style="color:#ef4444">*</span></label>
                        <input type="number" name="min_temp" id="minTemp"
                               class="form-control {{ $errors->has('min_temp') ? 'is-invalid' : '' }}"
                               value="{{ old('min_temp', $zone->min_temp ?? '') }}"
                               step="0.5" required
                               style="font-size:1.1rem;font-weight:700;color:#3b82f6;">
                        @error('min_temp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Suhu Maksimum (°C) <span style="color:#ef4444">*</span></label>
                        <input type="number" name="max_temp" id="maxTemp"
                               class="form-control {{ $errors->has('max_temp') ? 'is-invalid' : '' }}"
                               value="{{ old('max_temp', $zone->max_temp ?? '') }}"
                               step="0.5" required
                               style="font-size:1.1rem;font-weight:700;color:#ef4444;">
                        @error('max_temp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Keterangan</label>
                        <textarea name="description" class="form-control" rows="2"
                                  placeholder="Deskripsi singkat — isi, fungsi, atau catatan khusus zona ini">{{ old('description', $zone->description ?? '') }}</textarea>
                    </div>
                </div>

                <div class="flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        {{ isset($zone) ? 'Update Zona' : 'Simpan Zona' }}
                    </button>
                    <a href="{{ route('temperatures.zones') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Panduan suhu HACCP --}}
    <div>
        <div class="card" style="background:linear-gradient(135deg,#0f2035,#1a3350);">
            <div class="card-body">
                <h3 style="color:#fff;font-size:.9rem;margin-bottom:1rem;">
                    <i class="fa-solid fa-snowflake" style="color:#60a5fa;"></i>
                    Panduan Range Suhu (HACCP)
                </h3>
                @foreach([
                    ['🧊','Chiller',      '0°C ~ 4°C',   'Suhu paling aman untuk dairy, sayuran, bumbu WIP'],
                    ['❄️','Freezer',      '-18°C ~ -22°C','Protein beku, frozen food, WIP frozen'],
                    ['📦','Dry Storage',  '18°C ~ 30°C',  'Bahan kering, packaging, dry goods'],
                    ['🏪','Display',      '2°C ~ 8°C',    'Area display produk siap saji'],
                ] as [$ico,$nm,$range,$note])
                <div style="display:flex;gap:.65rem;margin-bottom:.875rem;align-items:flex-start;">
                    <span style="font-size:1rem;flex-shrink:0;margin-top:2px;">{{ $ico }}</span>
                    <div>
                        <div style="color:#fff;font-size:.8rem;font-weight:700;">{{ $nm }}</div>
                        <div style="color:#60a5fa;font-size:.82rem;font-weight:800;">{{ $range }}</div>
                        <div style="color:#64748b;font-size:.7rem;margin-top:2px;">{{ $note }}</div>
                    </div>
                </div>
                @endforeach
                <div style="border-top:1px solid rgba(255,255,255,.08);padding-top:.875rem;margin-top:.25rem;">
                    <div style="color:#94a3b8;font-size:.72rem;line-height:1.6;">
                        <i class="fa-solid fa-triangle-exclamation" style="color:#f59e0b;"></i>
                        Suhu di luar range akan otomatis dicatat sebagai <strong style="color:#fca5a5;">ABNORMAL</strong> dan memicu alert.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const defaults = @json($defaultRanges);
const types = @json(array_keys($typeConfig));

function selectType(type) {
    types.forEach(t => {
        const btn = document.getElementById('type-btn-'+t);
        const radio = document.querySelector(`[name=type][value="${t}"]`);
        if (btn) {
            btn.style.border = t===type ? '2px solid #3b82f6' : '2px solid #e2e8f0';
            btn.style.background = t===type ? '#eff6ff' : '#f8fafc';
        }
        if (radio) radio.checked = t===type;
    });
    setDefaults(type);
}

function setDefaults(type) {
    const d = defaults[type];
    if (!d) return;
    // Hanya isi jika kosong atau belum diubah
    const minEl = document.getElementById('minTemp');
    const maxEl = document.getElementById('maxTemp');
    if (minEl && (!minEl.value || confirm('Isi range suhu default untuk tipe ini?\n'+d.min+'°C ~ '+d.max+'°C'))) {
        minEl.value = d.min;
        maxEl.value = d.max;
    }
}

// Init selection
const currentType = '{{ old("type", $zone->type ?? "") }}';
if (currentType) selectType(currentType);
</script>
@endpush
@endsection
