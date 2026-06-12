<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = [
        'header_id',      // ← tambahkan ini
        'ingredient_id',
        'user_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'notes',
    ];

    protected $casts = [
        'quantity'     => 'decimal:2',
        'stock_before' => 'decimal:2',
        'stock_after'  => 'decimal:2',
    ];

    public function header()     { return $this->belongsTo(StockTransactionHeader::class, 'header_id'); }
    public function ingredient() { return $this->belongsTo(Ingredient::class); }
    public function user()       { return $this->belongsTo(User::class); }
}