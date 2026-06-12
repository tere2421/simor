<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\StaffProfile;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $week  = $request->week ?? now()->format('Y-\WW');
        $dates = $this->weekDates($week);

        $schedules = Schedule::with(['staffProfile', 'shift'])
            ->whereBetween('schedule_date', [$dates->first()->toDateString(), $dates->last()->toDateString()])
            ->get()
            ->groupBy(['staff_profile_id', fn($s) => $s->schedule_date->toDateString()]);

        $staff  = StaffProfile::where('is_active', true)->orderBy('name')->get();
        $shifts = Shift::orderBy('start_time')->get();

        $hasSaved    = Schedule::whereBetween('schedule_date', [$dates->first(), $dates->last()])->exists();
        $pendingCount = 0;

        return view('schedules.index', compact(
            'schedules', 'staff', 'shifts', 'dates', 'week', 'hasSaved', 'pendingCount'
        ));
    }

    /**
     * Bulk save — seluruh grid dalam satu klik
     */
    public function bulkSave(Request $request)
    {
        $week  = $request->week ?? now()->format('Y-\WW');
        $dates = $this->weekDates($week);
        $data  = $request->input('schedules', []);

        foreach ($data as $staffId => $days) {
            foreach ($days as $dateStr => $shiftId) {
                if (empty($shiftId)) {
                    // Hapus jadwal jika dikosongkan
                    Schedule::where('staff_profile_id', $staffId)
                        ->whereDate('schedule_date', $dateStr)
                        ->delete();
                    continue;
                }

                Schedule::updateOrCreate(
                    [
                        'staff_profile_id' => $staffId,
                        'schedule_date'    => $dateStr,
                    ],
                    [
                        'shift_id'    => $shiftId,
                        'status'      => 'approved',
                        'approved_by' => auth()->id(),
                        'week_number' => Carbon::parse($dateStr)->weekOfYear,
                    ]
                );
            }
        }

        return redirect()->route('schedules.index', ['week' => $week])
            ->with('success', 'Jadwal mingguan berhasil disimpan.');
    }

    /**
     * Export ke Excel / CSV / PDF
     */
    public function export(Request $request)
    {
        $week   = $request->week ?? now()->format('Y-\WW');
        $format = $request->format ?? 'excel';
        $dates  = $this->weekDates($week);

        $staff = StaffProfile::where('is_active', true)->orderBy('name')->get();
        $shifts = Shift::orderBy('start_time')->get();

        $schedules = Schedule::with(['staffProfile', 'shift'])
            ->whereBetween('schedule_date', [$dates->first()->toDateString(), $dates->last()->toDateString()])
            ->get()
            ->groupBy(['staff_profile_id', fn($s) => $s->schedule_date->toDateString()]);

        $filename = 'Jadwal_SIMOR_' . str_replace('-', '_', $week);

        if ($format === 'csv') {
            return $this->exportCsv($staff, $shifts, $dates, $schedules, $filename);
        }

        if ($format === 'pdf') {
            return $this->exportPdf($staff, $shifts, $dates, $schedules, $week, $filename);
        }

        // Default: Excel
        return $this->exportExcel($staff, $shifts, $dates, $schedules, $filename);
    }

    private function exportCsv($staff, $shifts, $dates, $schedules, $filename)
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function () use ($staff, $shifts, $dates, $schedules) {
            $handle = fopen('php://output', 'w');

            // Header row
            $head = ['Nama Staff', 'Posisi', 'Tipe'];
            foreach ($dates as $d) {
                $head[] = $d->locale('id')->isoFormat('ddd, D MMM');
            }
            $head[] = 'Total Shift';
            fputcsv($handle, $head);

            // Data rows
            foreach ($staff as $s) {
                $row = [$s->name, $s->position, $s->shift_type];
                $cnt = 0;
                foreach ($dates as $d) {
                    $sc = $schedules[$s->id][$d->toDateString()][0] ?? null;
                    $row[] = $sc ? $sc->shift->name : 'Libur';
                    if ($sc) $cnt++;
                }
                $row[] = $cnt;
                fputcsv($handle, $row);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportExcel($staff, $shifts, $dates, $schedules, $filename)
    {
        $headers = [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xlsx\"",
        ];

        // Buat XML Excel sederhana (SpreadsheetML)
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                           xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
        $xml .= '<Worksheet ss:Name="Jadwal"><Table>';

        // Header
        $xml .= '<Row>';
        foreach (['Nama Staff','Posisi','Tipe'] as $h) {
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($h) . '</Data></Cell>';
        }
        foreach ($dates as $d) {
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($d->locale('id')->isoFormat('ddd, D MMM')) . '</Data></Cell>';
        }
        $xml .= '<Cell><Data ss:Type="String">Total Shift</Data></Cell>';
        $xml .= '</Row>';

        // Data
        foreach ($staff as $s) {
            $xml .= '<Row>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($s->name)     . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($s->position) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($s->shift_type) . '</Data></Cell>';
            $cnt = 0;
            foreach ($dates as $d) {
                $sc = $schedules[$s->id][$d->toDateString()][0] ?? null;
                $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($sc ? $sc->shift->name : 'Libur') . '</Data></Cell>';
                if ($sc) $cnt++;
            }
            $xml .= '<Cell><Data ss:Type="Number">' . $cnt . '</Data></Cell>';
            $xml .= '</Row>';
        }

        $xml .= '</Table></Worksheet></Workbook>';

        return response($xml, 200, $headers);
    }

    private function exportPdf($staff, $shifts, $dates, $schedules, $week, $filename)
    {
        $html = view('schedules.export-pdf', compact('staff', 'shifts', 'dates', 'schedules', 'week'))->render();

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        }

        // Fallback: print view
        return response($html)->header('Content-Type', 'text/html');
    }

    // === CRUD lama (tetap ada) ===
    public function create()
    {
        $staff  = StaffProfile::where('is_active', true)->orderBy('name')->get();
        $shifts = Shift::all();
        return view('schedules.create', compact('staff', 'shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'staff_profile_id' => 'required|exists:staff_profiles,id',
            'shift_id'         => 'required|exists:shifts,id',
            'schedule_date'    => 'required|date',
        ]);

        Schedule::updateOrCreate(
            ['staff_profile_id' => $request->staff_profile_id, 'schedule_date' => $request->schedule_date],
            ['shift_id' => $request->shift_id, 'status' => 'approved', 'approved_by' => auth()->id(),
             'week_number' => now()->parse($request->schedule_date)->weekOfYear]
        );

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function approve(Schedule $schedule)
    {
        $schedule->update(['status' => 'approved', 'approved_by' => auth()->id()]);
        return back()->with('success', 'Jadwal disetujui.');
    }

    public function cancel(Schedule $schedule)
    {
        $schedule->update(['status' => 'cancelled']);
        return back()->with('success', 'Jadwal dibatalkan.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Jadwal dihapus.');
    }

    private function weekDates(string $week): \Illuminate\Support\Collection
    {
        [$yr, $wk] = explode('-W', $week);
        $start = Carbon::now()->setISODate((int)$yr, (int)$wk)->startOfWeek();
        return collect(range(0, 6))->map(fn($d) => $start->copy()->addDays($d));
    }
}
