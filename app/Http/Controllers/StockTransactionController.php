<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\StockTransaction;
use App\Models\StockTransactionHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransactionController extends Controller
{
    /**
     * List semua transaksi — dikelompokkan per header
     */
    public function index(Request $request)
    {
        $query = StockTransactionHeader::with(['user', 'lines.ingredient'])
            ->latest('transaction_date')
            ->latest('id');

        if ($request->type)  $query->where('type', $request->type);
        if ($request->date)  $query->whereDate('transaction_date', $request->date);
        if ($request->search) {
            $query->where('transaction_code', 'like', '%'.$request->search.'%')
                  ->orWhere('notes', 'like', '%'.$request->search.'%');
        }

        $headers     = $query->paginate(15)->withQueryString();
        $ingredients = Ingredient::where('is_active', true)->orderBy('name')->get();

        return view('stocks.index', compact('headers', 'ingredients'));
    }

    /**
     * Form bulk input
     */
    public function create()
    {
        $ingredients = Ingredient::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($i) => [
                'id'            => $i->id,
                'name'          => $i->name,
                'unit'          => $i->unit,
                'current_stock' => $i->current_stock,
                'category'      => $i->category->name,
            ]);

        return view('stocks.create', compact('ingredients'));
    }

    /**
     * Simpan bulk transaksi
     */
    public function store(Request $request)
    {
        $request->validate([
            'type'              => 'required|in:in,out',
            'transaction_date'  => 'required|date',
            'notes'             => 'nullable|string|max:500',
            'lines'             => 'required|array|min:1',
            'lines.*.ingredient_id' => 'required|exists:ingredients,id',
            'lines.*.quantity'      => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            // Cek duplikat ingredient dalam satu transaksi
            $ids = array_column($request->lines, 'ingredient_id');
            if (count($ids) !== count(array_unique($ids))) {
                return back()
                    ->withInput()
                    ->withErrors(['lines' => 'Terdapat bahan baku yang duplikat dalam satu transaksi.']);
            }

            // Validasi stok keluar
            if ($request->type === 'out') {
                foreach ($request->lines as $idx => $line) {
                    $ing = Ingredient::find($line['ingredient_id']);
                    if ($line['quantity'] > $ing->current_stock) {
                        return back()->withInput()->withErrors([
                            "lines.{$idx}.quantity" =>
                                "Stok {$ing->name} tidak cukup. Tersedia: {$ing->current_stock} {$ing->unit}"
                        ]);
                    }
                }
            }

            // Buat header
            $header = StockTransactionHeader::create([
                'transaction_code' => StockTransactionHeader::generateCode($request->type),
                'user_id'          => auth()->id(),
                'type'             => $request->type,
                'transaction_date' => $request->transaction_date,
                'notes'            => $request->notes,
            ]);

            // Buat baris transaksi + update stok
            foreach ($request->lines as $line) {
                $ing         = Ingredient::find($line['ingredient_id']);
                $stockBefore = $ing->current_stock;
                $stockAfter  = $request->type === 'in'
                    ? $stockBefore + $line['quantity']
                    : $stockBefore - $line['quantity'];

                StockTransaction::create([
                    'header_id'     => $header->id,
                    'ingredient_id' => $line['ingredient_id'],
                    'user_id'       => auth()->id(),
                    'type'          => $request->type,
                    'quantity'      => $line['quantity'],
                    'stock_before'  => $stockBefore,
                    'stock_after'   => $stockAfter,
                    'notes'         => $line['notes'] ?? null,
                ]);

                $ing->update(['current_stock' => $stockAfter]);
            }

            DB::commit();

            return redirect()->route('stocks.index')
                ->with('success', "Transaksi {$header->transaction_code} berhasil disimpan — {$header->totalItems()} item.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['lines' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    /**
     * Detail satu header transaksi
     */
    public function show(StockTransactionHeader $stock)
    {
        $stock->load(['user', 'lines.ingredient.category']);
        return view('stocks.show', compact('stock'));
    }
}
