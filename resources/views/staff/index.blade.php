@extends('layouts.app')
@section('title', 'Data Staff')
@section('breadcrumb', 'SIMOR / Staff')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="font-bold" style="font-size:1.25rem;">Data Staff</h2>
        <p class="text-sm text-muted">Kelola akun dan data karyawan Hangry Indonesia</p>
    </div>
    <a href="{{ route('staff.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-user-plus"></i> Tambah Staff
    </a>
</div>

<div class="card mb-4">
    <form method="GET" class="card-body" style="display:flex;gap:.75rem;align-items:flex-end;">
        <div style="flex:2;min-width:200px;">
            <label class="form-label">Cari nama staff</label>
            <input type="text" name="search" class="form-control"
                   placeholder="Nama staff..." value="{{ request('search') }}">
        </div>
        <button class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
        <a href="{{ route('staff.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Staff</th><th>NIP</th><th>Posisi</th>
                <th>Tipe</th><th>Role</th><th>Email Login</th><th>Bergabung</th><th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staff as $s)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:.75rem;">
                        <div style="width:36px;height:36px;border-radius:50%;
                            background:linear-gradient(135deg,#3b82f6,#1e40af);
                            display:flex;align-items:center;justify-content:center;
                            font-size:.8rem;font-weight:800;color:#fff;flex-shrink:0;">
                            {{ strtoupper(substr($s->name,0,1)) }}
                        </div>
                        <div class="font-semibold text-sm">{{ $s->name }}</div>
                    </div>
                </td>
                <td><span class="badge badge-gray">{{ $s->employee_id }}</span></td>
                <td class="text-sm">{{ $s->position }}</td>
                <td>
                    <span class="badge {{ $s->shift_type==='FT' ? 'badge-info' : 'badge-gray' }}">
                        {{ $s->shift_type==='FT' ? 'Full Time' : 'Daily Worker' }}
                    </span>
                </td>
                <td>
                    <span style="padding:2px 10px;border-radius:4px;font-size:.72rem;font-weight:700;
                        background:{{ $s->user->role==='SM' ? '#fef9c3' : ($s->user->role==='PIC' ? '#dbeafe' : '#dcfce7') }};
                        color:{{ $s->user->role==='SM' ? '#854d0e' : ($s->user->role==='PIC' ? '#1e40af' : '#166534') }};">
                        {{ $s->user->role }}
                    </span>
                </td>
                <td class="text-xs" style="color:#64748b;">{{ $s->user->email }}</td>
                <td class="text-sm">{{ $s->join_date?->format('d/m/Y') ?? '-' }}</td>
                <td>
                    <div class="flex gap-2">
                        {{-- Reset Password --}}
                        <button onclick="openReset({{ $s->id }}, '{{ $s->name }}')"
                                class="btn btn-sm btn-warning" title="Reset Password">
                            <i class="fa-solid fa-key"></i>
                        </button>
                        {{-- Nonaktifkan --}}
                        <form method="POST" action="{{ route('staff.destroy', $s) }}"
                              onsubmit="return confirm('Nonaktifkan {{ $s->name }}?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" title="Nonaktifkan">
                                <i class="fa-solid fa-ban"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted" style="padding:3rem;">
                    Belum ada data staff
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="card-body" style="border-top:1px solid #f1f5f9;">
        {{ $staff->links() }}
    </div>
</div>

{{-- Modal Reset Password --}}
<div id="resetModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:.75rem;padding:2rem;width:400px;max-width:calc(100vw - 2rem);box-shadow:0 20px 60px rgba(0,0,0,.3);">
        <h3 style="font-size:1.1rem;font-weight:800;color:#1e293b;margin-bottom:.25rem;">
            <i class="fa-solid fa-key" style="color:#f59e0b;"></i> Reset Password
        </h3>
        <p class="text-sm text-muted" style="margin-bottom:1.25rem;" id="resetModalName"></p>
        <form method="POST" id="resetForm">
            @csrf @method('PATCH')
            <div class="form-group">
                <label class="form-label">Password Baru <span style="color:#ef4444">*</span></label>
                <input type="password" name="password" class="form-control"
                       placeholder="Min. 6 karakter" required minlength="6">
            </div>
            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label">Konfirmasi Password <span style="color:#ef4444">*</span></label>
                <input type="password" name="password_confirmation" class="form-control"
                       placeholder="Ulangi password" required>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-warning" style="flex:1;justify-content:center;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Password Baru
                </button>
                <button type="button" onclick="closeReset()" class="btn btn-secondary">Batal</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openReset(staffId, name) {
    document.getElementById('resetModal').style.display = 'flex';
    document.getElementById('resetModalName').textContent = 'Reset password untuk: ' + name;
    document.getElementById('resetForm').action = '/staff/' + staffId + '/reset-password';
}
function closeReset() {
    document.getElementById('resetModal').style.display = 'none';
}
// Close on backdrop click
document.getElementById('resetModal').addEventListener('click', function(e) {
    if (e.target === this) closeReset();
});
</script>
@endpush
@endsection
