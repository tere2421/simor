<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\ChecklistRecord;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    public function index(Request $request)
    {
        $date    = $request->date    ?? today()->toDateString();
        $session = $request->session ?? 'pagi';

        // Ambil item: yang berlaku untuk shift ini + yang 'all'
        $items = ChecklistItem::forShift($session)->get();

        // Ambil records yang sudah diisi
        $records = ChecklistRecord::where('date', $date)
            ->where('session', $session)
            ->pluck('is_done', 'checklist_item_id');

        $donePct = $items->count() > 0
            ? round(($records->filter()->count() / $items->count()) * 100)
            : 0;

        // Kelompokkan item: khusus shift vs berlaku semua
        $itemsByShift = $items->groupBy('shift');

        return view('checklists.index', compact(
            'items', 'itemsByShift', 'records', 'date', 'session', 'donePct'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'    => 'required|date',
            'session' => 'required|in:pagi,siang,malam',
            'items'   => 'nullable|array',
        ]);

        $items = ChecklistItem::forShift($request->session)->get();

        foreach ($items as $item) {
            ChecklistRecord::updateOrCreate(
                [
                    'checklist_item_id' => $item->id,
                    'date'              => $request->date,
                    'session'           => $request->session,
                ],
                [
                    'user_id' => auth()->id(),
                    'is_done' => in_array($item->id, $request->items ?? []),
                ]
            );
        }

        return redirect()->route('checklists.index', [
            'date'    => $request->date,
            'session' => $request->session,
        ])->with('success', 'Checklist shift ' . ucfirst($request->session) . ' berhasil disimpan.');
    }

    public function history(Request $request)
    {
        $records = ChecklistRecord::with(['item', 'user'])
            ->whereDate('date', $request->date ?? today())
            ->get()
            ->groupBy('session');

        return view('checklists.history', compact('records'));
    }
}
