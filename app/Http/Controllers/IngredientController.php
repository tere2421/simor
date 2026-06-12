<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Category;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index(Request $request)
    {
        $query = Ingredient::with('category')->where('is_active', true);

        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->status === 'kritis') {
            $query->whereColumn('current_stock', '<=', 'min_stock_threshold');
        }

        $ingredients = $query->orderBy('name')->paginate(15)->withQueryString();
        $categories  = Category::orderBy('name')->get();

        return view('ingredients.index', compact('ingredients', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('ingredients.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'category_id'          => 'required|exists:categories,id',
            'unit'                 => 'required|string|max:50',
            'current_stock'        => 'required|numeric|min:0',
            'min_stock_threshold'  => 'required|numeric|min:0',
            'storage_location'     => 'nullable|string|max:100',
            'unit_price'           => 'nullable|numeric|min:0',
            'expiry_date'          => 'nullable|date',
        ]);

        Ingredient::create($request->all());

        return redirect()->route('ingredients.index')
            ->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function show(Ingredient $ingredient)
    {
        $ingredient->load(['category', 'stockTransactions.user']);
        $transactions = $ingredient->stockTransactions()->latest()->paginate(10);
        return view('ingredients.show', compact('ingredient', 'transactions'));
    }

    public function edit(Ingredient $ingredient)
    {
        $categories = Category::orderBy('name')->get();
        return view('ingredients.edit', compact('ingredient', 'categories'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'category_id'          => 'required|exists:categories,id',
            'unit'                 => 'required|string|max:50',
            'min_stock_threshold'  => 'required|numeric|min:0',
            'storage_location'     => 'nullable|string|max:100',
            'unit_price'           => 'nullable|numeric|min:0',
            'expiry_date'          => 'nullable|date',
        ]);

        $ingredient->update($request->except('current_stock'));

        return redirect()->route('ingredients.index')
            ->with('success', 'Bahan baku berhasil diperbarui.');
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->update(['is_active' => false]);
        return redirect()->route('ingredients.index')
            ->with('success', 'Bahan baku berhasil dinonaktifkan.');
    }
}
