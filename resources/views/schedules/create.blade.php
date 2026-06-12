@extends('layouts.app')
@section('title','Tambah Jadwal')
@section('breadcrumb','SIMOR / Penjadwalan / Tambah Jadwal')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="font-bold" style="font-size:1.25rem;">Tambah Jadwal Staff</h2>
    <a href="{{ route('schedules.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>
<div class="card" style="max-width:560px;">
    <div class="card-header"><h3>Form Jadwal Baru</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('schedules.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Staff <span style="color:#ef4444">*</span></label>
                <select name="staff_profile_id" class="form-control" required>
                    <option value="">-- Pilih Staff --</option>
                    @foreach($staff as $s)
                        <option value="{{ $s->id }}" {{ old('staff_profile_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }} — {{ $s->position }} ({{ $s->shift_type }})
                        </option>
                    @endforeach
                </select>
                @error('staff_profile_id')<div class="invalid-feedback" style="display:block">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Shift <span style="color:#ef4444">*</span></label>
                @foreach($shifts as $sh)
                    <label style="display:flex;align-items:center;gap:.75rem;padding:.7rem;border:1.5px solid #e2e8f0;border-radius:.5rem;cursor:pointer;margin-bottom:.5rem;transition:border-color .15s;">
                        <input type="radio" name="shift_id" value="{{ $sh->id }}" {{ old('shift_id') == $sh->id ? 'checked' : '' }} style="accent-color:#3b82f6;">
                        <div>
                            <div class="font-semibold">{{ $sh->name }}</div>
                            <div class="text-xs text-muted">{{ \Carbon\Carbon::parse($sh->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($sh->end_time)->format('H:i') }} | {{ $sh->description }}</div>
                        </div>
                    </label>
                @endforeach
                @error('shift_id')<div class="invalid-feedback" style="display:block">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal <span style="color:#ef4444">*</span></label>
                <input type="date" name="schedule_date" class="form-control"
                       value="{{ old('schedule_date', today()->toDateString()) }}" required>
                @error('schedule_date')<div class="invalid-feedback" style="display:block">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Opsional...">{{ old('notes') }}</textarea>
            </div>
            <div class="flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Jadwal</button>
                <a href="{{ route('schedules.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
