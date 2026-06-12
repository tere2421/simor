@extends('layouts.app')
@section('title', 'Input Transaksi Stok')
@section('breadcrumb', 'SIMOR / Stok / Transaksi / Input Baru')

@section('content')

<style>
.line-row {
    display:grid;
    grid-template-columns: 2fr 120px 100px 1fr 40px;
    gap:.6rem; align-items:start;
    padding:.75rem 1rem;
    border-bottom:1px solid #f1f5f9;
    background:#fff;
    transition:background .12s;
}
.line-row:hover { background:#fafbff; }
.line-row:first-child { border-top:1px solid #f1f5f9; }

.search-wrap { position:relative; }
.search-input {
    width:100%; padding:.55rem .85rem .55rem 2.2rem;
    border:1.5px solid #e2e8f0; border-radius:.5rem;
    font-size:.85rem; color:#1e293b; background:#fff;
    transition:border-color .15s, box-shadow .15s; outline:none;
}
.search-input:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.12); }
.search-icon { position:absolute; left:.7rem; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:.8rem; }
.dropdown-list {
    position:absolute; top:calc(100% + 4px); left:0; right:0; z-index:100;
    background:#fff; border:1.5px solid #e2e8f0; border-radius:.5rem;
    box-shadow:0 8px 24px rgba(0,0,0,.1); max-height:220px; overflow-y:auto;
    display:none;
}
.dropdown-list.open { display:block; }
.dropdown-item {
    padding:.6rem 1rem; cursor:pointer; font-size:.83rem;
    border-bottom:1px solid #f8fafc; display:flex; justify-content:space-between; align-items:center;
    transition:background .1s;
}
.dropdown-item:hover, .dropdown-item.focused { background:#eff6ff; }
.dropdown-item:last-child { border-bottom:none; }
.dropdown-item .item-name { font-weight:600; color:#1e293b; }
.dropdown-item .item-meta { font-size:.72rem; color:#94a3b8; }
.dropdown-item .item-stock { font-size:.75rem; font-weight:700; }
.dropdown-empty { padding:1rem; text-align:center; color:#94a3b8; font-size:.82rem; }

.qty-input {
    width:100%; padding:.55rem .65rem; text-align:right;
    border:1.5px solid #e2e8f0; border-radius:.5rem;
    font-size:.88rem; font-weight:700; color:#1e293b;
    outline:none; transition:border-color .15s;
}
.qty-input:focus { border-color:#3b82f6; }
.unit-badge {
    display:flex; align-items:center; justify-content:center;
    background:#f1f5f9; border-radius:.5rem; font-size:.78rem;
    font-weight:700; color:#475569; height:36px; padding:0 .5rem;
}
.note-input {
    width:100%; padding:.5rem .75rem;
    border:1.5px solid #e2e8f0; border-radius:.5rem;
    font-size:.8rem; color:#475569; outline:none;
    transition:border-color .15s;
}
.note-input:focus { border-color:#3b82f6; }
.btn-remove {
    width:32px; height:32px; border-radius:.4rem;
    background:#fef2f2; border:1px solid #fecaca; color:#ef4444;
    cursor:pointer; display:flex; align-items:center; justify-content:center;
    font-size:.8rem; transition:all .15s; flex-shrink:0; margin-top:2px;
}
.btn-remove:hover { background:#ef4444; color:#fff; }

/* type toggle */
.type-btn {
    flex:1; padding:.75rem; border-radius:.5rem; font-weight:700;
    font-size:.9rem; cursor:pointer; border:2px solid transparent;
    display:flex; align-items:center; justify-content:center; gap:.5rem;
    transition:all .15s;
}
.type-btn.in  { background:#f0fdf4; border-color:#86efac; color:#166534; }
.type-btn.out { background:#fef2f2; border-color:#fca5a5; color:#991b1b; }
.type-btn.inactive { background:#f8fafc; border-color:#e2e8f0; color:#94a3b8; }

/* stock indicator */
.stock-bar { height:4px; border-radius:9999px; background:#e2e8f0; margin-top:4px; overflow:hidden; }
.stock-bar-fill { height:100%; border-radius:9999px; transition:width .3s; }

.line-header {
    display:grid;
    grid-template-columns: 2fr 120px 100px 1fr 40px;
    gap:.6rem; padding:.5rem 1rem;
    background:#f8fafc; border-bottom:2px solid #e2e8f0;
    font-size:.72rem; font-weight:700; text-transform:uppercase;
    letter-spacing:.05em; color:#64748b;
}
</style>

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="font-bold" style="font-size:1.25rem;color:#1e293b;">Input Transaksi Stok</h2>
        <p class="text-sm text-muted">Satu transaksi bisa berisi banyak item sekaligus</p>
    </div>
    <a href="{{ route('stocks.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

<form method="POST" action="{{ route('stocks.store') }}" id="bulkForm">
@csrf

<div class="grid gap-4" style="grid-template-columns:1fr 340px;align-items:start;">

    {{-- LEFT: Item lines --}}
    <div>
        {{-- Tipe Transaksi --}}
        <div class="card mb-4">
            <div class="card-header"><h3>Tipe Transaksi</h3></div>
            <div class="card-body" style="display:flex;gap:.75rem;">
                <label style="flex:1;cursor:pointer;">
                    <input type="radio" name="type" value="in" checked style="display:none;" id="typeIn">
                    <div class="type-btn in" id="btnIn" onclick="setType('in')">
                        <i class="fa-solid fa-arrow-down"></i> STOK MASUK
                    </div>
                </label>
                <label style="flex:1;cursor:pointer;">
                    <input type="radio" name="type" value="out" style="display:none;" id="typeOut">
                    <div class="type-btn inactive" id="btnOut" onclick="setType('out')">
                        <i class="fa-solid fa-arrow-up"></i> STOK KELUAR
                    </div>
                </label>
            </div>
        </div>

        {{-- Item Lines --}}
        <div class="card">
            <div class="card-header" style="justify-content:space-between;">
                <h3><i class="fa-solid fa-list" style="color:#3b82f6;"></i> Daftar Item</h3>
                <span class="badge badge-info" id="itemCountBadge">0 item</span>
            </div>

            {{-- Header kolom --}}
            <div class="line-header">
                <div>Bahan Baku</div>
                <div style="text-align:right;">Jumlah</div>
                <div style="text-align:center;">Satuan</div>
                <div>Catatan Item</div>
                <div></div>
            </div>

            {{-- Rows container --}}
            <div id="linesContainer"></div>

            {{-- Add row button --}}
            <div style="padding:.875rem 1rem;border-top:1px solid #f1f5f9;">
                <button type="button" onclick="addRow()" class="btn btn-secondary" style="width:100%;">
                    <i class="fa-solid fa-plus"></i> Tambah Item
                </button>
            </div>

            {{-- Validation error --}}
            @error('lines')
                <div style="padding:.75rem 1rem;background:#fef2f2;border-top:1px solid #fca5a5;color:#991b1b;font-size:.82rem;">
                    <i class="fa-solid fa-circle-xmark"></i> {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    {{-- RIGHT: Info & Submit --}}
    <div style="position:sticky;top:80px;">

        {{-- Tanggal & Catatan --}}
        <div class="card mb-4">
            <div class="card-header"><h3>Info Transaksi</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Tanggal Transaksi <span style="color:#ef4444">*</span></label>
                    <input type="date" name="transaction_date" class="form-control"
                           value="{{ today()->toDateString() }}" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Catatan Transaksi</label>
                    <textarea name="notes" class="form-control" rows="3"
                              placeholder="Penerimaan dari supplier, pemakaian harian, dll.">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Summary --}}
        <div class="card mb-4" id="summaryCard" style="display:none;">
            <div class="card-header"><h3><i class="fa-solid fa-receipt" style="color:#f59e0b;"></i> Ringkasan</h3></div>
            <div id="summaryList" style="max-height:260px;overflow-y:auto;"></div>
            <div style="padding:.75rem 1.25rem;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;">
                <span class="text-sm font-semibold">Total Item</span>
                <span class="font-bold" id="summaryTotal" style="color:#3b82f6;">0</span>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;padding:.875rem;" id="submitBtn">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Transaksi
        </button>
        <a href="{{ route('stocks.index') }}" class="btn btn-secondary" style="width:100%;margin-top:.5rem;justify-content:center;">
            Batal
        </a>
    </div>

</div>
</form>

{{-- Hidden template row --}}
<template id="rowTemplate">
    <div class="line-row" id="row-__IDX__">
        <div class="search-wrap">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text"
                   class="search-input"
                   placeholder="Cari nama bahan baku..."
                   autocomplete="off"
                   oninput="filterDropdown(this, __IDX__)"
                   onfocus="openDropdown(__IDX__)"
                   onblur="delayClose(__IDX__)">
            <input type="hidden" name="lines[__IDX__][ingredient_id]" id="ing-__IDX__">
            <div class="dropdown-list" id="dd-__IDX__"></div>
        </div>
        <div>
            <input type="number" name="lines[__IDX__][quantity]"
                   class="qty-input" placeholder="0"
                   step="0.01" min="0.01"
                   oninput="updateSummary(); checkQty(this, __IDX__)">
            <div class="stock-bar" id="bar-__IDX__" style="display:none;">
                <div class="stock-bar-fill" id="bar-fill-__IDX__"></div>
            </div>
        </div>
        <div class="unit-badge" id="unit-__IDX__">—</div>
        <input type="text" name="lines[__IDX__][notes]" class="note-input" placeholder="opsional...">
        <button type="button" class="btn-remove" onclick="removeRow(__IDX__)">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
</template>

@push('scripts')
<script>
// Data bahan baku dari server
const INGREDIENTS = @json($ingredients);

let rowCount = 0;
let currentType = 'in';

// ── TYPE TOGGLE ──────────────────────────────────────────
function setType(type) {
    currentType = type;
    document.getElementById('typeIn').checked  = type === 'in';
    document.getElementById('typeOut').checked = type === 'out';
    document.getElementById('btnIn').className  = 'type-btn ' + (type === 'in'  ? 'in'  : 'inactive');
    document.getElementById('btnOut').className = 'type-btn ' + (type === 'out' ? 'out' : 'inactive');

    // Recheck all qty
    document.querySelectorAll('.qty-input').forEach(inp => {
        const idx = inp.name.match(/\d+/)?.[0];
        if (idx) checkQty(inp, idx);
    });
    updateSummary();
}

// ── ADD / REMOVE ROW ─────────────────────────────────────
function addRow() {
    const idx = rowCount++;
    const tpl = document.getElementById('rowTemplate').innerHTML
        .replace(/__IDX__/g, idx);
    const wrap = document.createElement('div');
    wrap.innerHTML = tpl;
    document.getElementById('linesContainer').appendChild(wrap.firstElementChild);
    buildDropdown(idx);
    updateCount();
}

function removeRow(idx) {
    document.getElementById('row-' + idx)?.remove();
    updateCount();
    updateSummary();
}

// ── DROPDOWN SEARCH ──────────────────────────────────────
const selectedIngredients = {}; // idx → ingredient obj

function buildDropdown(idx, filter = '') {
    const dd  = document.getElementById('dd-' + idx);
    const sel = selectedIngredients[idx];
    const used = Object.entries(selectedIngredients)
        .filter(([i]) => i != idx).map(([, v]) => v?.id);

    const filtered = INGREDIENTS.filter(i =>
        (!filter || i.name.toLowerCase().includes(filter.toLowerCase())) &&
        !used.includes(i.id)
    );

    if (!filtered.length) {
        dd.innerHTML = '<div class="dropdown-empty">Tidak ditemukan</div>';
        return;
    }

    dd.innerHTML = filtered.map(i => {
        const pct  = i.current_stock > 0
            ? Math.min(100, Math.round((i.current_stock / (i.current_stock * 2)) * 100))
            : 0;
        const clr  = i.current_stock <= 0 ? '#ef4444' : '#22c55e';
        return `
        <div class="dropdown-item" onmousedown="selectIngredient(${idx}, ${i.id})">
            <div>
                <div class="item-name">${i.name}</div>
                <div class="item-meta">${i.category}</div>
            </div>
            <div style="text-align:right;">
                <div class="item-stock" style="color:${clr};">${i.current_stock} ${i.unit}</div>
                <div class="item-meta">tersedia</div>
            </div>
        </div>`;
    }).join('');
}

function openDropdown(idx) {
    buildDropdown(idx, document.querySelector(`#row-${idx} .search-input`)?.value || '');
    document.getElementById('dd-' + idx)?.classList.add('open');
}

function filterDropdown(inp, idx) {
    buildDropdown(idx, inp.value);
    document.getElementById('dd-' + idx)?.classList.add('open');
    // Clear selection if user typed
    if (selectedIngredients[idx]) {
        selectedIngredients[idx] = null;
        document.getElementById('ing-' + idx).value = '';
        document.getElementById('unit-' + idx).textContent = '—';
        document.getElementById('bar-' + idx).style.display = 'none';
        updateSummary();
    }
}

function delayClose(idx) {
    setTimeout(() => document.getElementById('dd-' + idx)?.classList.remove('open'), 200);
}

function selectIngredient(idx, ingId) {
    const ing = INGREDIENTS.find(i => i.id === ingId);
    if (!ing) return;
    selectedIngredients[idx] = ing;

    document.getElementById('ing-' + idx).value = ing.id;
    document.querySelector(`#row-${idx} .search-input`).value = ing.name;
    document.getElementById('dd-' + idx)?.classList.remove('open');
    document.getElementById('unit-' + idx).textContent = ing.unit;

    // Stock bar
    const bar     = document.getElementById('bar-' + idx);
    const barFill = document.getElementById('bar-fill-' + idx);
    bar.style.display = 'block';
    const pct = ing.current_stock > 0 ? Math.min(100, 100) : 0;
    const clr = ing.current_stock <= 0 ? '#ef4444' : '#22c55e';
    barFill.style.width = '100%';
    barFill.style.background = clr;

    // Recheck qty
    const qtyInp = document.querySelector(`#row-${idx} .qty-input`);
    if (qtyInp?.value) checkQty(qtyInp, idx);
    updateSummary();
}

// ── QTY VALIDATION ───────────────────────────────────────
function checkQty(inp, idx) {
    const ing = selectedIngredients[idx];
    if (!ing || currentType === 'in') {
        inp.style.borderColor = '';
        inp.title = '';
        return;
    }
    const qty = parseFloat(inp.value) || 0;
    if (qty > ing.current_stock) {
        inp.style.borderColor = '#ef4444';
        inp.title = `Melebihi stok! Tersedia: ${ing.current_stock} ${ing.unit}`;
        // stock bar merah
        const fill = document.getElementById('bar-fill-' + idx);
        if (fill) { fill.style.width = '100%'; fill.style.background = '#ef4444'; }
    } else if (qty > 0) {
        inp.style.borderColor = '#22c55e';
        inp.title = '';
        const pct  = Math.min(100, Math.round((qty / ing.current_stock) * 100));
        const fill = document.getElementById('bar-fill-' + idx);
        if (fill) { fill.style.width = pct + '%'; fill.style.background = pct > 80 ? '#f59e0b' : '#3b82f6'; }
    } else {
        inp.style.borderColor = '';
    }
}

// ── SUMMARY ──────────────────────────────────────────────
function updateSummary() {
    const rows = document.querySelectorAll('.line-row');
    const items = [];

    rows.forEach(row => {
        const idx = row.id.replace('row-', '');
        const ing = selectedIngredients[idx];
        const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
        if (ing && qty > 0) items.push({ ing, qty });
    });

    const card = document.getElementById('summaryCard');
    const list = document.getElementById('summaryList');
    const tot  = document.getElementById('summaryTotal');

    if (!items.length) { card.style.display = 'none'; return; }
    card.style.display = 'block';
    tot.textContent = items.length + ' item';

    const color = currentType === 'in' ? '#166534' : '#991b1b';
    const sign  = currentType === 'in' ? '+' : '-';
    const bg    = currentType === 'in' ? '#f0fdf4' : '#fef2f2';

    list.innerHTML = items.map(({ ing, qty }) => `
        <div style="display:flex;justify-content:space-between;align-items:center;
                    padding:.6rem 1.25rem;border-bottom:1px solid #f1f5f9;font-size:.82rem;">
            <div>
                <div style="font-weight:600;color:#1e293b;">${ing.name}</div>
                <div style="font-size:.7rem;color:#94a3b8;">${ing.category}</div>
            </div>
            <div style="text-align:right;">
                <div style="font-weight:800;color:${color};">${sign}${qty} ${ing.unit}</div>
                <div style="font-size:.7rem;color:#94a3b8;">
                    → ${currentType==='in' ? (ing.current_stock+qty).toFixed(2) : (ing.current_stock-qty).toFixed(2)} ${ing.unit}
                </div>
            </div>
        </div>`).join('');
}

// ── HELPERS ──────────────────────────────────────────────
function updateCount() {
    const n = document.querySelectorAll('.line-row').length;
    document.getElementById('itemCountBadge').textContent = n + ' item';
}

// Submit validation
document.getElementById('bulkForm').addEventListener('submit', function(e) {
    const rows   = document.querySelectorAll('.line-row');
    let hasError = false;

    rows.forEach(row => {
        const idx  = row.id.replace('row-', '');
        const ingId = document.getElementById('ing-' + idx)?.value;
        const qty   = parseFloat(row.querySelector('.qty-input')?.value) || 0;
        if (!ingId || qty <= 0) hasError = true;
    });

    if (rows.length === 0) {
        e.preventDefault();
        alert('Tambahkan minimal 1 item terlebih dahulu.');
        return;
    }
    if (hasError) {
        e.preventDefault();
        alert('Pastikan semua baris sudah diisi dengan bahan baku dan jumlah yang valid.');
        return;
    }

    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
    btn.disabled = true;
});

// Auto-add first row on load
addRow();
</script>
@endpush
@endsection
