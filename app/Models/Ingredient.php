<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = [
        'category_id', 'name', 'unit', 'current_stock',
        'min_stock_threshold', 'storage_location', 'unit_price', 'expiry_date', 'is_active'
    ];

    protected $casts = [
        'current_stock'       => 'decimal:2',
        'min_stock_threshold' => 'decimal:2',
        'unit_price'          => 'decimal:2',
        'expiry_date'         => 'date',
        'is_active'           => 'boolean',
    ];

    public function category()          { return $this->belongsTo(Category::class); }
    public function stockTransactions() { return $this->hasMany(StockTransaction::class); }

    public function isCritical(): bool {
        return $this->current_stock <= $this->min_stock_threshold;
    }

    public function expiryDaysLeft(): ?int {
        if (!$this->expiry_date) return null;
        return now()->diffInDays($this->expiry_date, false);
    }

    public function stockStatus(): string {
        if ($this->current_stock == 0)                          return 'habis';
        if ($this->current_stock <= $this->min_stock_threshold) return 'kritis';
        if ($this->current_stock <= $this->min_stock_threshold * 1.5) return 'rendah';
        return 'aman';
    }
}
