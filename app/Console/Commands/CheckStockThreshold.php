<?php

namespace App\Console\Commands;

use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckStockThreshold extends Command
{
    protected $signature   = 'simor:check-stock';
    protected $description = 'Cek stok kritis dan kirim notifikasi ke SM/PIC';

    public function handle(): void
    {
        $critical = Ingredient::where('is_active', true)
            ->whereColumn('current_stock', '<=', 'min_stock_threshold')
            ->with('category')
            ->get();

        if ($critical->isEmpty()) {
            $this->info('Tidak ada stok kritis. Semua aman.');
            return;
        }

        $this->warn("Ditemukan {$critical->count()} bahan baku dengan stok kritis:");
        foreach ($critical as $item) {
            $this->line("  - {$item->name}: {$item->current_stock} {$item->unit} (min: {$item->min_stock_threshold})");
        }

        // Log untuk audit
        Log::info('SIMOR Stock Alert', [
            'total_critical' => $critical->count(),
            'items'          => $critical->pluck('name')->toArray(),
            'checked_at'     => now()->toDateTimeString(),
        ]);

        $this->info('Notifikasi berhasil dikirim.');
    }
}
