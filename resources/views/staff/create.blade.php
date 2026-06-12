@extends('layouts.app')
@section('title','Tambah Staff')
@section('breadcrumb','SIMOR / Staff / Tambah')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="font-bold" style="font-size:1.25rem;">Tambah Staff Baru</h2>
    <a href="{{ route('staff.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>
<div class="card" style="max-width:720px;">
    <div class="card-header"><h3>Form Registrasi Staff</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('staff.store') }}">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="form-group" style="grid-column:span 2;">
                    <label class="form-label">Nama Lengkap <span style="color:#ef4444">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback" style="display:block">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email <span style="color:#ef4444">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback" style="display:block">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">ID Karyawan <span style="color:#ef4444">*</span></label>
                    <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id') }}" placeholder="HG013" required>
                    @error('employee_id')<div class="invalid-feedback" style="display:block">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Password <span style="color:#ef4444">*</span></label>
                    <input type="password" name="password" class="form-control" required>
                    @error('password')<div class="invalid-feedback" style="display:block">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password <span style="color:#ef4444">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Role Sistem <span style="color:#ef4444">*</span></label>
                    <select name="role" class="form-control" required>
                        <option value="Staff" {{ old('role') === 'Staff' ? 'selected' : '' }}>Staff</option>
                        <option value="PIC"   {{ old('role') === 'PIC'   ? 'selected' : '' }}>PIC</option>
                        <option value="SM"    {{ old('role') === 'SM'    ? 'selected' : '' }}>Store Manager (SM)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Posisi <span style="color:#ef4444">*</span></label>
                    <select name="position" class="form-control" required>
                        @foreach(['Store Manager','PIC','Senior Staff','Junior Staff'] as $p)
                            <option value="{{ $p }}" {{ old('position') === $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tipe Karyawan <span style="color:#ef4444">*</span></label>
                    <select name="shift_type" class="form-control" required>
                        <option value="FT" {{ old('shift_type') === 'FT' ? 'selected' : '' }}>FT — Full Time</option>
                        <option value="DW" {{ old('shift_type') === 'DW' ? 'selected' : '' }}>DW — Daily Worker</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">No. HP</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="08xx">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Bergabung</label>
                    <input type="date" name="join_date" class="form-control" value="{{ old('join_date', today()->toDateString()) }}">
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Daftarkan Staff</button>
                <a href="{{ route('staff.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
