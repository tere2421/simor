@extends('layouts.app')
@section('title', isset($task) ? 'Edit Task' : 'Tambah Task')
@section('breadcrumb','SIMOR / Checklist / '.( isset($task) ? 'Edit' : 'Tambah').' Task')
@section('content')

<div class="flex items-center justify-between mb-6">
    <h2 class="font-bold" style="font-size:1.25rem;">{{ isset($task) ? 'Edit Task' : 'Tambah Task Baru' }}</h2>
    <a href="{{ route('manager-tasks.manage') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card" style="max-width:680px;">
    <div class="card-header"><h3>Form Task</h3></div>
    <div class="card-body">
        <form method="POST"
              action="{{ isset($task) ? route('manager-tasks.update', $task) : route('manager-tasks.store') }}">
            @csrf
            @if(isset($task)) @method('PUT') @endif

            <div class="form-group">
                <label class="form-label">Judul Task <span style="color:#ef4444">*</span></label>
                <input type="text" name="title" class="form-control {{ $errors->has('title') ? 'is-invalid':'' }}"
                       value="{{ old('title', $task->title ?? '') }}"
                       placeholder="Contoh: Check Brand Complaint" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">
                    URL Task
                    <span style="color:#94a3b8;font-weight:400;font-size:.75rem;">
                        — link ke GForm, GSheet, Notion, dll (opsional)
                    </span>
                </label>
                <div style="position:relative;">
                    <i class="fa-solid fa-link" style="position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:.82rem;"></i>
                    <input type="url" name="url" class="form-control {{ $errors->has('url') ? 'is-invalid':'' }}"
                           style="padding-left:2.5rem;"
                           value="{{ old('url', $task->url ?? '') }}"
                           placeholder="https://forms.google.com/... atau https://docs.google.com/...">
                </div>
                @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="text-xs text-muted" style="margin-top:.35rem;">
                    Jika diisi, judul task akan menjadi link yang bisa diklik langsung
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Untuk Role <span style="color:#ef4444">*</span></label>
                    <select name="role_target" class="form-control" required>
                        <option value="SM"   {{ old('role_target', $task->role_target ?? '') === 'SM'   ? 'selected':'' }}>SM (Store Manager)</option>
                        <option value="PIC"  {{ old('role_target', $task->role_target ?? '') === 'PIC'  ? 'selected':'' }}>PIC</option>
                        <option value="both" {{ old('role_target', $task->role_target ?? 'both') === 'both' ? 'selected':'' }}>SM & PIC (keduanya)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Frekuensi <span style="color:#ef4444">*</span></label>
                    <select name="frequency" class="form-control" required>
                        @foreach([
                            'daily'=>'Setiap Hari',
                            'monday'=>'Setiap Senin',
                            'tuesday'=>'Setiap Selasa',
                            'wednesday'=>'Setiap Rabu',
                            'thursday'=>'Setiap Kamis',
                            'friday'=>'Setiap Jumat',
                            'weekly'=>'Mingguan (Senin)',
                            'monthly'=>'Bulanan (Tgl 1)',
                        ] as $val => $label)
                            <option value="{{ $val }}" {{ old('frequency', $task->frequency ?? 'daily') === $val ? 'selected':'' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <input type="text" name="category" class="form-control"
                           value="{{ old('category', $task->category ?? '') }}"
                           placeholder="Monitoring, Audit, SDM, dll."
                           list="categoryList">
                    <datalist id="categoryList">
                        @foreach(['Monitoring','Audit','SDM','Kualitas','Operasional','Pengadaan','Platform','Laporan','Analitik','Equipment','Food Safety','Stok'] as $cat)
                            <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                </div>

                <div class="form-group">
                    <label class="form-label">Urutan</label>
                    <input type="number" name="order" class="form-control" min="0"
                           value="{{ old('order', $task->order ?? 0) }}">
                </div>
            </div>

            @if(isset($task))
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-control">
                    <option value="1" {{ ($task->is_active ?? true) ? 'selected':'' }}>Aktif</option>
                    <option value="0" {{ !($task->is_active ?? true) ? 'selected':'' }}>Nonaktif</option>
                </select>
            </div>
            @endif

            <div class="flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    {{ isset($task) ? 'Update Task' : 'Simpan Task' }}
                </button>
                <a href="{{ route('manager-tasks.manage') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
