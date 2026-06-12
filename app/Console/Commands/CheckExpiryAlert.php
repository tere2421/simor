<?php

namespace App\Console\Commands;

use App\Models\Ingredient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckExpiryAlert extends Command
{
    protected $signature   = 'simor:check-expiry';
    protected $description = 'Cek bahan baku mendekati kadaluarsa (H-7, H-3, H-1)';

    public function handle(): void
    {
        $thresholds = [7, 3, 1];

        foreach ($thresholds as $days) {
            $expiring = Ingredient::where('is_active', true)
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', now()->addDays($days)->toDateString())
                ->get();

            if ($expiring->isNotEmpty()) {
                $this->warn("Bahan kadaluarsa H-{$days} ({$expiring->count()} item):");
                foreach ($expiring as $item) {
                    $this->line("  - {$item->name} | exp: {$item->expiry_date->format('d/m/Y')} | stok: {$item->current_stock} {$item->unit}");
                }
            }
        }

        // Sudah kadaluarsa
        $expired = Ingredient::where('is_active', true)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', today())
            ->get();

        if ($expired->isNotEmpty()) {
            $this->error("KADALUARSA ({$expired->count()} item) — SEGERA TINDAK LANJUTI:");
            foreach ($expired as $item) {
                $this->line("  - {$item->name} | exp: {$item->expiry_date->format('d/m/Y')}");
            }
        }

        Log::info('SIMOR Expiry Check selesai.', ['checked_at' => now()->toDateTimeString()]);
        $this->info('Pengecekan kadaluarsa selesai.');
    }
}
