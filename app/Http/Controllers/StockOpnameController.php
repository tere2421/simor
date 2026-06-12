<?php
namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\StockOpname;
use App\Models\StockOpnameLine;
use App\Models\StockTransaction;
use App\Models\StockTransactionHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index()
    {
        $opnames = StockOpname::with('user')->latest('opname_date')->paginate(12);
        return view('opnames.index', compact('opnames'));
    }

    /**
     * Form stock opname — tampilkan semua bahan baku + stok sistem
     */
    public function create()
    {
        $ingredients = Ingredient::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $period = now()->format('Y-m');
        return view('opnames.create', compact('ingredients', 'period'));
    }

    /**
     * Download template Excel/CSV untuk diisi offline
     */
    public function downloadTemplate()
    {
        $ingredients = Ingredient::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_stock_opname_'.now()->format('Y_m').'.csv"',
        ];

        $callback = function () use ($ingredients) {
            $out = fopen('php://output', 'w');
            // BOM untuk Excel bisa baca UTF-8
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'Nama Bahan Baku', 'Kategori', 'Satuan', 'Stok Sistem', 'Stok Fisik (ISI INI)', 'Catatan']);
            foreach ($ingredients as $ing) {
                fputcsv($out, [
                    $ing->id,
                    $ing->name,
                    $ing->category->name,
                    $ing->unit,
                    $ing->current_stock,
                    '', // diisi oleh user
                    '',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Upload CSV/Excel hasil opname & simpan
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file'         => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
            'opname_date'  => 'required|date',
            'notes'        => 'nullable|string',
        ]);

        $file    = $request->file('file');
        $ext     = strtolower($file->getClientOriginalExtension());
        $period  = now()->parse($request->opname_date)->format('Y-m');
        $rows    = [];

        // Parse CSV
        if (in_array($ext, ['csv', 'txt'])) {
            $handle = fopen($file->getRealPath(), 'r');
            $header = fgetcsv($handle); // skip header
            while (($row = fgetcsv($handle)) !== false) {
                if (empty($row[0]) || !is_numeric($row[0])) continue;
                if ($row[5] === '' || $row[5] === null) continue; // skip baris kosong
                $rows[] = [
                    'ingredient_id' => (int) $row[0],
                    'actual_stock'  => (float) $row[5],
                    'notes'         => $row[6] ?? null,
                ];
            }
            fclose($handle);
        }
        // Parse XLSX pakai PhpSpreadsheet jika ada, fallback ke manual
        elseif (in_array($ext, ['xlsx', 'xls'])) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $sheet       = $spreadsheet->getActiveSheet();
                $data        = $sheet->toArray();
                array_shift($data); // skip header
                foreach ($data as $row) {
                    if (empty($row[0]) || !is_numeric($row[0])) continue;
                    if ($row[5] === '' || $row[5] === null) continue;
                    $rows[] = [
                        'ingredient_id' => (int) $row[0],
                        'actual_stock'  => (float) $row[5],
                        'notes'         => $row[6] ?? null,
                    ];
                }
            } catch (\Exception $e) {
                return back()->withErrors(['file' => 'Gagal membaca file Excel: ' . $e->getMessage()]);
            }
        }

        if (empty($rows)) {
            return back()->withErrors(['file' => 'Tidak ada data valid di file. Pastikan kolom "Stok Fisik" sudah diisi.']);
        }

        DB::beginTransaction();
        try {
            $opname = StockOpname::create([
                'opname_code'  => StockOpname::generateCode($period),
                'user_id'      => auth()->id(),
                'opname_date'  => $request->opname_date,
                'status'       => 'submitted',
                'period'       => $period,
                'notes'        => $request->notes,
            ]);

            foreach ($rows as $row) {
                $ing = Ingredient::find($row['ingredient_id']);
                if (!$ing) continue;

                StockOpnameLine::create([
                    'opname_id'     => $opname->id,
                    'ingredient_id' => $row['ingredient_id'],
                    'system_stock'  => $ing->current_stock,
                    'actual_stock'  => $row['actual_stock'],
                    'notes'         => $row['notes'],
                ]);
            }

            DB::commit();
            return redirect()->route('opnames.show', $opname)
                ->with('success', "Draft opname {$opname->opname_code} berhasil dibuat — {$opname->lines()->count()} item.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['file' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    /**
     * Input manual (tanpa upload file) — bulk form
     */
    public function store(Request $request)
    {
        $request->validate([
            'opname_date' => 'required|date',
            'period'      => 'required|string',
            'notes'       => 'nullable|string',
            'lines'       => 'required|array|min:1',
            'lines.*.ingredient_id' => 'required|exists:ingredients,id',
            'lines.*.actual_stock'  => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $opname = StockOpname::create([
                'opname_code' => StockOpname::generateCode($request->period),
                'user_id'     => auth()->id(),
                'opname_date' => $request->opname_date,
                'status'      => 'submitted',
                'period'      => $request->period,
                'notes'       => $request->notes,
            ]);

            foreach ($request->lines as $line) {
                $ing = Ingredient::find($line['ingredient_id']);
                StockOpnameLine::create([
                    'opname_id'     => $opname->id,
                    'ingredient_id' => $line['ingredient_id'],
                    'system_stock'  => $ing->current_stock,
                    'actual_stock'  => $line['actual_stock'],
                    'notes'         => $line['notes'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('opnames.show', $opname)
                ->with('success', "Opname {$opname->opname_code} berhasil disimpan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['lines' => $e->getMessage()]);
        }
    }

    public function show(StockOpname $opname)
    {
        $opname->load(['user', 'approver', 'lines.ingredient.category']);
        return view('opnames.show', compact('opname'));
    }

    /**
     * Approve & apply — update semua stok sesuai hasil opname
     */
    public function approve(StockOpname $opname)
    {
        if ($opname->status === 'approved') {
            return back()->with('error', 'Opname ini sudah di-approve.');
        }

        DB::beginTransaction();
        try {
            // Buat transaction header
            $header = StockTransactionHeader::create([
                'transaction_code' => 'OP-ADJ-' . $opname->opname_code,
                'user_id'          => auth()->id(),
                'type'             => 'in', // adjustment
                'transaction_date' => $opname->opname_date,
                'notes'            => "Stock opname adjustment — {$opname->opname_code}",
            ]);

            foreach ($opname->lines as $line) {
                $ing  = $line->ingredient;
                $diff = $line->actual_stock - $ing->current_stock;

                // Buat transaksi adjustment
                \App\Models\StockTransaction::create([
                    'header_id'     => $header->id,
                    'ingredient_id' => $ing->id,
                    'user_id'       => auth()->id(),
                    'type'          => $diff >= 0 ? 'in' : 'out',
                    'quantity'      => abs($diff),
                    'stock_before'  => $ing->current_stock,
                    'stock_after'   => $line->actual_stock,
                    'notes'         => "Koreksi opname {$opname->opname_code}",
                ]);

                // Update stok
                $ing->update(['current_stock' => $line->actual_stock]);
            }

            $opname->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
            ]);

            DB::commit();
            return redirect()->route('opnames.show', $opname)
                ->with('success', "Opname {$opname->opname_code} di-approve. Semua stok diperbarui.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['approve' => $e->getMessage()]);
        }
    }
}
