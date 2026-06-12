<?php
namespace App\Http\Controllers;

use App\Models\TemperatureRecord;
use App\Models\TemperatureZone;
use Illuminate\Http\Request;

class TemperatureController extends Controller
{
    // ── RECORDS ──────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = TemperatureRecord::with(['zone', 'user'])->latest('recorded_at');

        if ($request->zone_id) $query->where('zone_id', $request->zone_id);
        if ($request->abnormal) $query->where('is_abnormal', true);
        if ($request->date)    $query->whereDate('recorded_at', $request->date);

        $records    = $query->paginate(20)->withQueryString();
        $zones      = TemperatureZone::orderBy('name')->get();
        $zoneStatus = TemperatureZone::with('latestRecord')->orderBy('name')->get();

        return view('temperatures.index', compact('records', 'zones', 'zoneStatus'));
    }

    public function create()
    {
        $zones = TemperatureZone::orderBy('name')->get();
        return view('temperatures.create', compact('zones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'zone_id'     => 'required|exists:temperature_zones,id',
            'temperature' => 'required|numeric|between:-50,100',
            'notes'       => 'nullable|string',
            'recorded_at' => 'nullable|date',
        ]);

        TemperatureRecord::create([
            'zone_id'     => $request->zone_id,
            'user_id'     => auth()->id(),
            'temperature' => $request->temperature,
            'notes'       => $request->notes,
            'recorded_at' => $request->recorded_at ?? now(),
        ]);

        return redirect()->route('temperatures.index')
            ->with('success', 'Data suhu berhasil dicatat.');
    }

    // ── ZONES CRUD ────────────────────────────────────────────
    public function zones()
    {
        $zones = TemperatureZone::withCount('records')
            ->with('latestRecord')
            ->orderBy('name')
            ->get();
        return view('temperatures.zones', compact('zones'));
    }

    public function createZone()
    {
        return view('temperatures.zone-form', ['zone' => null]);
    }

    public function storeZone(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:temperature_zones,name',
            'type'        => 'required|in:chiller,freezer,dry_storage,display,other',
            'location'    => 'nullable|string|max:150',
            'min_temp'    => 'required|numeric|between:-50,50',
            'max_temp'    => 'required|numeric|between:-50,50|gt:min_temp',
            'description' => 'nullable|string',
        ]);

        TemperatureZone::create($request->all());
        return redirect()->route('temperatures.zones')
            ->with('success', "Zona {$request->name} berhasil ditambahkan.");
    }

    public function editZone(TemperatureZone $zone)
    {
        return view('temperatures.zone-form', compact('zone'));
    }

    public function updateZone(Request $request, TemperatureZone $zone)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:temperature_zones,name,'.$zone->id,
            'type'        => 'required|in:chiller,freezer,dry_storage,display,other',
            'location'    => 'nullable|string|max:150',
            'min_temp'    => 'required|numeric|between:-50,50',
            'max_temp'    => 'required|numeric|between:-50,50|gt:min_temp',
            'description' => 'nullable|string',
        ]);

        $zone->update($request->all());
        return redirect()->route('temperatures.zones')
            ->with('success', "Zona {$zone->name} berhasil diperbarui.");
    }

    public function destroyZone(TemperatureZone $zone)
    {
        if ($zone->records()->count() > 0) {
            return back()->with('error', 'Zona tidak bisa dihapus karena sudah memiliki riwayat pencatatan suhu.');
        }
        $zone->delete();
        return back()->with('success', 'Zona berhasil dihapus.');
    }
}
