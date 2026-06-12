@extends('layouts.app')
@section('title','Tambah Kode Shift')
@section('breadcrumb','SIMOR / Penjadwalan / Kode Shift / Tambah')
@section('content')

<div class="flex items-center justify-between mb-6">
    <h2 class="font-bold" style="font-size:1.25rem;">Tambah Kode Shift</h2>
    <a href="{{ route('shifts.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="grid gap-4" style="grid-template-columns:1fr 1fr;align-items:start;">
    <div class="card">
        <div class="card-header"><h3>Form Kode Shift</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('shifts.store') }}" id="shiftForm">
                @csrf

                <div class="form-group">
                    <label class="form-label">Kode Shift <span style="color:#ef4444">*</span></label>
                    <div style="position:relative;">
                        <input type="text" name="code" id="codeInput"
                               class="form-control {{ $errors->has('code') ? 'is-invalid':'' }}"
                               style="font-family:monospace;font-size:1.2rem;font-weight:800;
                                      letter-spacing:3px;padding:.75rem 1rem;"
                               value="{{ old('code') }}"
                               placeholder="H080800"
                               maxlength="7"
                               oninput="previewCode(this.value.toUpperCase());this.value=this.value.toUpperCase()"
                               required>
                    </div>
                    @error('code')<div class="invalid-feedback" style="display:block">{{ $message }}</div>@enderror
                    <div class="text-xs text-muted" style="margin-top:.35rem;">
                        Format: H + 2 digit durasi + 4 digit jam masuk (HHMM)
                    </div>
                </div>

                {{-- Preview realtime --}}
                <div id="preview" style="display:none;background:#f0fdf4;border:1.5px solid #86efac;
                     border-radius:.5rem;padding:1rem 1.25rem;margin-bottom:1.25rem;">
                    <div class="text-xs font-semibold" style="color:#166534;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem;">
                        Preview Shift
                    </div>
                    <div style="display:flex;gap:2rem;align-items:center;">
                        <div style="text-align:center;">
                            <div style="font-size:.72rem;color:#94a3b8;">Durasi</div>
                            <div style="font-size:1.5rem;font-weight:800;color:#166534;" id="prevDur">—</div>
                        </div>
                        <div style="font-size:1.5rem;color:#94a3b8;">→</div>
                        <div style="text-align:center;">
                            <div style="font-size:.72rem;color:#94a3b8;">Masuk</div>
                            <div style="font-size:1.5rem;font-weight:800;color:#22c55e;" id="prevIn">—</div>
                        </div>
                        <div style="font-size:1.5rem;color:#94a3b8;">–</div>
                        <div style="text-align:center;">
                            <div style="font-size:.72rem;color:#94a3b8;">Keluar</div>
                            <div style="font-size:1.5rem;font-weight:800;color:#ef4444;" id="prevOut">—</div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Keterangan</label>
                    <input type="text" name="description" class="form-control"
                           value="{{ old('description') }}"
                           placeholder="Contoh: Shift reguler pagi weekend">
                </div>

                <button type="submit" class="btn btn-primary w-full" style="justify-content:center;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Kode Shift
                </button>
            </form>
        </div>
    </div>

    {{-- Contoh kode --}}
    <div>
        <div class="card">
            <div class="card-header"><h3>Contoh Kode Shift</h3></div>
            <div class="card-body" style="padding:0;">
                @foreach([
                    ['H080700','8 jam','07:00','15:00'],
                    ['H080800','8 jam','08:00','16:00'],
                    ['H081200','8 jam','12:00','20:00'],
                    ['H081600','8 jam','16:00','00:00'],
                    ['H060700','6 jam','07:00','13:00'],
                    ['H061200','6 jam','12:00','18:00'],
                ] as [$code,$dur,$in,$out])
                <div style="display:flex;align-items:center;padding:.75rem 1.25rem;border-bottom:1px solid #f1f5f9;gap:1rem;cursor:pointer;"
                     onclick="document.getElementById('codeInput').value='{{ $code }}';previewCode('{{ $code }}')"
                     onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <span style="font-family:monospace;font-weight:800;font-size:.95rem;
                        background:#f1f5f9;padding:3px 10px;border-radius:5px;color:#1e293b;min-width:90px;">
                        {{ $code }}
                    </span>
                    <span class="text-sm text-muted">{{ $dur }}</span>
                    <span style="margin-left:auto;font-size:.85rem;font-weight:700;color:#334155;">{{ $in }} – {{ $out }}</span>
                </div>
                @endforeach
                <div style="padding:.75rem 1.25rem;font-size:.75rem;color:#94a3b8;">
                    Klik untuk mengisi form otomatis
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewCode(val) {
    const match = val.match(/^H(\d{2})(\d{2})(\d{2})$/);
    if (!match) { document.getElementById('preview').style.display='none'; return; }

    const dur  = parseInt(match[1]);
    const inH  = parseInt(match[2]);
    const inM  = parseInt(match[3]);

    if (dur < 1 || dur > 24 || inH > 23 || inM > 59) {
        document.getElementById('preview').style.display='none';
        return;
    }

    const totalMin = inH * 60 + inM + dur * 60;
    const outH  = Math.floor(totalMin / 60) % 24;
    const outM  = totalMin % 60;
    const fmt = n => n.toString().padStart(2,'0');

    document.getElementById('preview').style.display = '';
    document.getElementById('prevDur').textContent = dur + ' jam';
    document.getElementById('prevIn').textContent  = fmt(inH) + ':' + fmt(inM);
    document.getElementById('prevOut').textContent = fmt(outH) + ':' + fmt(outM);
}

// Init jika ada old value
const oldCode = '{{ old("code") }}';
if (oldCode) previewCode(oldCode);
</script>
@endpush
@endsection
