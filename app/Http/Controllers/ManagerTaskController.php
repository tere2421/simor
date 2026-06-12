<?php
namespace App\Http\Controllers;

use App\Models\ManagerTaskList;
use App\Models\ManagerTaskRecord;
use Illuminate\Http\Request;

class ManagerTaskController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $date = $request->date ?? today()->toDateString();

        // SM lihat semua, PIC hanya lihat task PIC & both
        $query = ManagerTaskList::where('is_active', true);
        if ($user->isPIC()) {
            $query->whereIn('role_target', ['PIC', 'both']);
        }

        $tasks = $query->orderBy('frequency')->orderBy('order')->orderBy('title')->get();

        // Records hari ini milik user ini
        $records = ManagerTaskRecord::where('user_id', $user->id)
            ->where('date', $date)
            ->pluck('is_done', 'task_id');

        // Histori pencatatan — semua record hari ini (semua user)
        $history = ManagerTaskRecord::with('user')
            ->where('date', $date)
            ->where('is_done', true)
            ->orderByDesc('updated_at')
            ->get()
            ->groupBy('task_id');

        // Progress
        $total = $tasks->count();
        $done  = $records->filter()->count();
        $pct   = $total > 0 ? round(($done / $total) * 100) : 0;

        // SM: pisah task SM-only vs PIC vs both
        $groupedSM  = collect();
        $groupedPIC = collect();
        if ($user->isSM()) {
            $groupedSM  = $tasks->whereIn('role_target', ['SM', 'both'])->groupBy('frequency');
            $groupedPIC = $tasks->whereIn('role_target', ['PIC', 'both'])->groupBy('frequency');
        } else {
            // PIC: semua jadi satu
            $groupedPIC = $tasks->groupBy('frequency');
        }

        return view('manager-tasks.index', compact(
            'tasks', 'records', 'history', 'date', 'pct', 'done', 'total',
            'user', 'groupedSM', 'groupedPIC'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'  => 'required|date',
            'tasks' => 'nullable|array',
        ]);

        $user  = auth()->user();
        $query = ManagerTaskList::where('is_active', true);
        if ($user->isPIC()) $query->whereIn('role_target', ['PIC', 'both']);
        $tasks = $query->get();

        foreach ($tasks as $task) {
            ManagerTaskRecord::updateOrCreate(
                ['task_id' => $task->id, 'user_id' => $user->id, 'date' => $request->date],
                ['is_done' => in_array($task->id, $request->tasks ?? [])]
            );
        }

        return redirect()->route('manager-tasks.index', ['date' => $request->date])
            ->with('success', 'Task berhasil disimpan.');
    }

    // ── CRUD Task (SM only) ──────────────────────────────────
    public function taskIndex()
    {
        $tasks = ManagerTaskList::orderBy('role_target')->orderBy('frequency')->orderBy('order')->paginate(25);
        return view('manager-tasks.manage', compact('tasks'));
    }

    public function taskCreate()
    {
        return view('manager-tasks.form', ['task' => null]);
    }

    public function taskStore(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'url'         => 'nullable|url|max:500',
            'role_target' => 'required|in:SM,PIC,both',
            'frequency'   => 'required|in:daily,monday,tuesday,wednesday,thursday,friday,weekly,monthly',
            'category'    => 'nullable|string|max:100',
            'order'       => 'nullable|integer|min:0',
        ]);

        ManagerTaskList::create([...$request->all(), 'created_by' => auth()->id()]);
        return redirect()->route('manager-tasks.manage')->with('success', 'Task berhasil ditambahkan.');
    }

    public function taskEdit(ManagerTaskList $task)
    {
        return view('manager-tasks.form', compact('task'));
    }

    public function taskUpdate(Request $request, ManagerTaskList $task)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'url'         => 'nullable|url|max:500',
            'role_target' => 'required|in:SM,PIC,both',
            'frequency'   => 'required|in:daily,monday,tuesday,wednesday,thursday,friday,weekly,monthly',
            'category'    => 'nullable|string|max:100',
            'order'       => 'nullable|integer|min:0',
        ]);

        $task->update($request->all());
        return redirect()->route('manager-tasks.manage')->with('success', 'Task berhasil diperbarui.');
    }

    public function taskDestroy(ManagerTaskList $task)
    {
        $task->records()->delete();
        $task->delete();
        return back()->with('success', 'Task berhasil dihapus.');
    }
}
