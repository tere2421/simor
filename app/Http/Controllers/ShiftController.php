<?php
namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::orderBy('start_time')->paginate(20);
        return view('shifts.index', compact('shifts'));
    }

    public function create()
    {
        return view('shifts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => [
                'required', 'string',
                'regex:/^H\d{6}$/',
                'unique:shifts,code',
            ],
            'description' => 'nullable|string|max:255',
        ], [
            'code.regex' => 'Format kode harus H + 2 digit durasi + 4 digit jam masuk. Contoh: H080800',
        ]);

        try {
            $parsed = Shift::parseCode(strtoupper($request->code));
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['code' => $e->getMessage()])->withInput();
        }

        // Cek konflik jam
        $conflict = Shift::where('start_time', $parsed['start_time'])
            ->where('duration_hours', $parsed['duration_hours'])
            ->exists();

        if ($conflict) {
            return back()->withErrors(['code' => 'Kode shift dengan jam dan durasi ini sudah ada.'])->withInput();
        }

        Shift::create([...$parsed, 'description' => $request->description]);

        return redirect()->route('shifts.index')
            ->with('success', "Kode shift {$request->code} berhasil ditambahkan.");
    }

    public function destroy(Shift $shift)
    {
        if ($shift->schedules()->count() > 0) {
            return back()->with('error', 'Kode shift tidak bisa dihapus karena sudah digunakan di jadwal.');
        }
        $shift->delete();
        return back()->with('success', 'Kode shift dihapus.');
    }
}
