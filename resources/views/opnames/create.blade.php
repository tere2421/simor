@extends('layouts.app')
@section('title','Stock Opname Baru')
@section('breadcrumb','SIMOR / Stok / Stock Opname / Baru')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="font-bold" style="font-size:1.25rem;">Input Stock Opname</h2>
    <a href="{{ route('opnames.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

{{-- Tab: Upload vs Manual --}}
<div style="display:flex;gap:0;margin-bottom:1.5rem;border-radius:.5rem;overflow:hidden;border:1.5px solid #e2e8f0;width:fit-content;">
    <button onclick="showTab('upload')" id="tab-upload"
        style="padding:.6rem 1.4rem;font-size:.875rem;font-weight:600;cursor:pointer;border:none;background:#0f2035;color:#fff;">
        <i class="fa-solid fa-file-arrow-up"></i> Upload CSV/Excel
    </button>
    <button onclick="showTab('manual')" id="tab-manual"
        style="padding:.6rem 1.4rem;font-size:.875rem;font-weight:600;cursor:pointer;border:none;background:#f1f5f9;color:#475569;">
        <i class="fa-solid fa-pen"></i> Input Manual
    </button>
</div>

{{-- ── TAB UPLOAD ── --}}
<div id="pane-upload">
    <div class="grid gap-4" style="grid-template-columns:1fr 320px;align-items:start;">
        <div class="card">
            <div class="card-header"><h3>Upload File CSV / Excel</h3></div>
            <div class="card-body">
                {{-- Step guide --}}
                <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:.5rem;padding:1rem 1.25rem;margin-bottom:1.5rem;">
                    <div class="font-semibold text-sm" style="color:#1e40af;margin-bottom:.5rem;">
                        <i class="fa-solid fa-circle-info"></i> Cara Mengisi:
                    </div>
                    <ol style="margin:0;padding-left:1.25rem;font-size:.82rem;color:#1e40af;line-height:2;">
                        <li>Download template CSV di tombol kanan atas</li>
                        <li>Buka di Excel / Google Sheets</li>
                        <li>Isi kolom <strong>"Stok Fisik (ISI INI)"</strong> dengan hasil hitung fisik</li>
                        <li>Simpan kembali sebagai CSV</li>
                        <li>Upload di form ini</li>
                    </ol>
                </div>

                <form method="POST" action="{{ route('opnames.upload') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Periode Opname <span style="color:#ef4444">*</span></label>
                        <input type="month" name="opname_date" class="form-control"
                               value="{{ now()->format('Y-m') }}" required
                               onchange="this.setAttribute('data-real', this.value+'-01')">
                        <input type="hidden" id="opname_date_hidden" name="opname_date">
                    </div>
                    <div class="form-group">
                        <label class="form-label">File CSV / Excel <span style="color:#ef4444">*</span></label>
                        <input type="file" name="file" class="form-control"
                               accept=".csv,.xlsx,.xls" required
                               onchange="showFileName(this)">
                        <div id="fileName" class="text-xs text-muted" style="margin-top:.4rem;"></div>
                        @error('file')<div class="invalid-feedback" style="display:block;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Opsional — kondisi opname, catatan khusus...">{{ old('notes') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-full" style="justify-content:center;"
                            onclick="fixDate()">
                        <i class="fa-solid fa-file-arrow-up"></i> Upload & Proses
                    </button>
                </form>
            </div>
        </div>

        <div>
            <div class="card" style="background:linear-gradient(135deg,#0f2035,#1a3350);color:#fff;">
                <div class="card-body">
                    <h3 style="color:#fff;margin-bottom:.75rem;">
                        <i class="fa-solid fa-file-csv" style="color:#22c55e;"></i> Format CSV
                    </h3>
                    <table style="width:100%;font-size:.72rem;color:#94a3b8;">
                        <tr style="color:#fff;font-weight:700;">
                            <td style="padding:4px 6px;">Kolom</td>
                            <td style="padding:4px 6px;">Keterangan</td>
                        </tr>
                        <tr><td style="padding:3px 6px;">ID</td><td style="padding:3px 6px;">ID bahan baku (jangan diubah)</td></tr>
                        <tr><td style="padding:3px 6px;">Nama</td><td style="padding:3px 6px;">Nama bahan baku</td></tr>
                        <tr><td style="padding:3px 6px;">Kategori</td><td style="padding:3px 6px;">Kategori</td></tr>
                        <tr><td style="padding:3px 6px;">Satuan</td><td style="padding:3px 6px;">Satuan</td></tr>
                        <tr><td style="padding:3px 6px;">Stok Sistem</td><td style="padding:3px 6px;">Stok di sistem (jangan diubah)</td></tr>
                        <tr style="color:#22c55e;font-weight:700;">
                            <td style="padding:3px 6px;">Stok Fisik</td><td style="padding:3px 6px;">← ISI INI hasil hitung fisik</td>
                        </tr>
                        <tr><td style="padding:3px 6px;">Catatan</td><td style="padding:3px 6px;">Opsional</td></tr>
                    </table>

                    <div style="margin-top:1rem;">
                        <a href="{{ route('opnames.download-template') }}"
                           class="btn btn-success" style="width:100%;justify-content:center;">
                            <i class="fa-solid fa-file-arrow-down"></i> Download Template
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── TAB MANUAL ── --}}
<div id="pane-manual" style="display:none;">
    <form method="POST" action="{{ route('opnames.store') }}">
        @csrf
        <div class="grid gap-4 mb-4" style="grid-template-columns:1fr 1fr 1fr;">
            <div class="form-group" style="margin:0;">
                <label class="form-label">Tanggal Opname <span style="color:#ef4444">*</span></label>
                <input type="date" name="opname_date" class="form-control"
                       value="{{ today()->toDateString() }}" required>
            </div>
            <div class="form-group" style="margin:0;">
                <label class="form-label">Periode <span style="color:#ef4444">*</span></label>
                <input type="month" name="period" class="form-control"
                       value="{{ now()->format('Y-m') }}" required>
            </div>
            <div class="form-group" style="margin:0;">
                <label class="form-label">Catatan</label>
                <input type="text" name="notes" class="form-control" placeholder="Opsional...">
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header" style="justify-content:space-between;">
                <h3>Semua Bahan Baku</h3>
                <div class="flex gap-2">
                    <button type="button" onclick="fillAll()" class="btn btn-sm btn-secondary">
                        <i class="fa-solid fa-copy"></i> Salin Semua dari Sistem
                    </button>
                </div>
            </div>

            {{-- Header --}}
            <div style="display:grid;grid-template-columns:2fr 1.2fr 1.2fr 1.5fr 1.2fr;
                        gap:.5rem;padding:.5rem 1rem;background:#f8fafc;
                        border-bottom:2px solid #e2e8f0;
                        font-size:.72rem;font-weight:700;text-transform:uppercase;
                        letter-spacing:.05em;color:#64748b;">
                <div>Bahan Baku</div>
                <div style="text-align:right;">Stok Sistem</div>
                <div style="text-align:right;color:#22c55e;">Stok Fisik ↓</div>
                <div>Selisih</div>
                <div>Catatan</div>
            </div>

            <div style="max-height:60vh;overflow-y:auto;">
                @foreach($ingredients as $i => $ing)
                <div style="display:grid;grid-template-columns:2fr 1.2fr 1.2fr 1.5fr 1.2fr;
                            gap:.5rem;padding:.6rem 1rem;border-bottom:1px solid #f1f5f9;
                            align-items:center;" id="row-{{ $ing->id }}">
                    <input type="hidden" name="lines[{{ $i }}][ingredient_id]" value="{{ $ing->id }}">
                    <div>
                        <div class="font-semibold text-sm">{{ $ing->name }}</div>
                        <div class="text-xs text-muted">{{ $ing->category->name }} · {{ $ing->unit }}</div>
                    </div>
                    <div style="text-align:right;font-weight:700;color:#64748b;">
                        {{ $ing->current_stock }} {{ $ing->unit }}
                    </div>
                    <div>
                        <input type="number" name="lines[{{ $i }}][actual_stock]"
                               class="form-control actual-input" step="0.01" min="0"
                               value="{{ $ing->current_stock }}"
                               data-system="{{ $ing->current_stock }}"
                               data-unit="{{ $ing->unit }}"
                               data-row="{{ $ing->id }}"
                               style="text-align:right;font-weight:700;"
                               oninput="calcDiff(this)">
                    </div>
                    <div id="diff-{{ $ing->id }}" style="font-size:.82rem;font-weight:700;color:#64748b;">
                        ±0 {{ $ing->unit }}
                    </div>
                    <input type="text" name="lines[{{ $i }}][notes]"
                           class="form-control" style="font-size:.78rem;"
                           placeholder="catatan...">
                </div>
                @endforeach
            </div>

            <div class="card-body" style="border-top:2px solid #f1f5f9;display:flex;justify-content:flex-end;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Stock Opname
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function showTab(tab) {
    document.getElementById('pane-upload').style.display = tab==='upload' ? '' : 'none';
    document.getElementById('pane-manual').style.display = tab==='manual' ? '' : 'none';
    document.getElementById('tab-upload').style.cssText = tab==='upload'
        ? 'padding:.6rem 1.4rem;font-size:.875rem;font-weight:600;cursor:pointer;border:none;background:#0f2035;color:#fff;'
        : 'padding:.6rem 1.4rem;font-size:.875rem;font-weight:600;cursor:pointer;border:none;background:#f1f5f9;color:#475569;';
    document.getElementById('tab-manual').style.cssText = tab==='manual'
        ? 'padding:.6rem 1.4rem;font-size:.875rem;font-weight:600;cursor:pointer;border:none;background:#0f2035;color:#fff;'
        : 'padding:.6rem 1.4rem;font-size:.875rem;font-weight:600;cursor:pointer;border:none;background:#f1f5f9;color:#475569;';
}

function showFileName(inp) {
    const el = document.getElementById('fileName');
    if (inp.files[0]) {
        el.textContent = '📄 ' + inp.files[0].name + ' (' + (inp.files[0].size/1024).toFixed(1) + ' KB)';
    }
}

function fixDate() {
    const monthInput = document.querySelector('[name=opname_date][type=month]');
    const hidden = document.getElementById('opname_date_hidden');
    if (monthInput && hidden) {
        hidden.value = monthInput.value + '-01';
        monthInput.disabled = true;
    }
}

function calcDiff(inp) {
    const system = parseFloat(inp.dataset.system) || 0;
    const actual = parseFloat(inp.value) || 0;
    const diff   = actual - system;
    const el     = document.getElementById('diff-' + inp.dataset.row);
    if (!el) return;
    el.textContent = (diff >= 0 ? '+' : '') + diff.toFixed(2) + ' ' + inp.dataset.unit;
    el.style.color = diff > 0 ? '#22c55e' : diff < 0 ? '#ef4444' : '#64748b';
}

function fillAll() {
    document.querySelectorAll('.actual-input').forEach(inp => {
        inp.value = inp.dataset.system;
        calcDiff(inp);
    });
}
</script>
@endpush
@endsection
