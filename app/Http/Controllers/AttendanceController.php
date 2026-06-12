<?php
namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\StaffProfile;
use App\Models\Schedule;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date  = $request->date ?? today()->toDateString();
        $month = $request->month ?? now()->format('Y-m');

        $query = Attendance::with(['staffProfile', 'schedule.shift', 'recorder'])
            ->when($request->date, fn($q) => $q->whereDate('date', $date))
            ->when(!$request->date, fn($q) => $q->whereRaw("DATE_FORMAT(date,'%Y-%m') = ?", [$month]))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest('date')->latest('created_at');

        $records = $query->paginate(20)->withQueryString();

        $summary = Attendance::selectRaw('status, COUNT(*) as total')
            ->whereRaw("DATE_FORMAT(date,'%Y-%m') = ?", [$month])
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('attendances.index', compact('records', 'date', 'month', 'summary'));
    }

    public function create()
    {
        $staff = StaffProfile::with('user')->where('is_active', true)->orderBy('name')->get();
        return view('attendances.create', compact('staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'staff_profile_id'    => 'required|exists:staff_profiles,id',
            'date'                => 'required|date',
            'status'              => 'required|in:terlambat,alpha,tidak_hadir,izin,sakit,pulang_awal,masalah_lain',
            'check_in_actual'     => 'nullable|required_if:status,terlambat|date_format:H:i',
            'check_out'           => 'nullable|required_if:status,pulang_awal|date_format:H:i',
            'late_minutes'        => 'nullable|integer|min:1',
            'problem_description' => 'nullable|string|max:500',
            'notes'               => 'nullable|string|max:500',
        ]);

        $lateMinutes = $request->late_minutes;
        if ($request->status === 'terlambat' && $request->check_in_actual) {
            $schedule = Schedule::with('shift')
                ->where('staff_profile_id', $request->staff_profile_id)
                ->whereDate('schedule_date', $request->date)
                ->first();

            if ($schedule) {
                $expected    = \Carbon\Carbon::parse($schedule->shift->start_time);
                $actual      = \Carbon\Carbon::parse($request->check_in_actual);
                if ($actual->gt($expected)) {
                    $lateMinutes = $expected->diffInMinutes($actual);
                }
            }
        }

        Attendance::updateOrCreate(
            [
                'staff_profile_id' => $request->staff_profile_id,
                'date'             => $request->date,
            ],
            [
                'schedule_id'         => $request->schedule_id,
                'status'              => $request->status,
                'check_in_actual'     => $request->check_in_actual,
                'check_out'           => $request->check_out,
                'late_minutes'        => $lateMinutes,
                'problem_description' => $request->problem_description,
                'notes'               => $request->notes,
                'recorded_by'         => auth()->id(),  // ← simpan siapa yang mencatat
            ]
        );

        return redirect()->route('attendances.index')
            ->with('success', 'Kendala kehadiran berhasil dicatat.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return back()->with('success', 'Catatan dihapus.');
    }
}
