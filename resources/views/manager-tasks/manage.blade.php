@extends('layouts.app')
@section('title','Kelola Task')
@section('breadcrumb','SIMOR / Checklist / Kelola Task')
@section('content')

<div class="flex items-center justify-between mb-6">
    <h2 class="font-bold" style="font-size:1.25rem;">Kelola Task Checklist</h2>
    <div class="flex gap-2">
        <a href="{{ route('manager-tasks.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-eye"></i> Lihat Checklist
        </a>
        <a href="{{ route('manager-tasks.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah Task
        </a>
    </div>
</div>

<div class="card">
    <table>
        <thead>
            <tr><th>Judul Task</th><th>Role</th><th>Frekuensi</th><th>Kategori</th><th>URL</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
            <tr>
                <td class="font-semibold">{{ $task->title }}</td>
                <td>
                    <span class="badge {{ $task->role_target==='SM' ? 'badge-danger' : ($task->role_target==='PIC' ? 'badge-info' : 'badge-success') }}">
                        {{ $task->role_target === 'both' ? 'SM & PIC' : $task->role_target }}
                    </span>
                </td>
                <td class="text-sm">{{ $task->frequencyLabel() }}</td>
                <td class="text-xs text-muted">{{ $task->category ?? '—' }}</td>
                <td>
                    @if($task->url)
                        <a href="{{ $task->url }}" target="_blank" class="btn btn-sm btn-secondary">
                            <i class="fa-solid fa-link"></i> Buka
                        </a>
                    @else <span class="text-muted text-xs">—</span> @endif
                </td>
                <td>
                    <span class="badge {{ $task->is_active ? 'badge-success' : 'badge-gray' }}">
                        {{ $task->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td>
                    <div class="flex gap-2">
                        <a href="{{ route('manager-tasks.edit', $task) }}" class="btn btn-sm btn-warning">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <form method="POST" action="{{ route('manager-tasks.destroy', $task) }}"
                              onsubmit="return confirm('Hapus task ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted" style="padding:3rem;">Belum ada task</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="card-body" style="border-top:1px solid #f1f5f9;">{{ $tasks->links() }}</div>
</div>
@endsection
