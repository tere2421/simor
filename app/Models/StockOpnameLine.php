<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StockOpnameLine extends Model
{
    protected $fillable = [
        'opname_id','ingredient_id','system_stock','actual_stock','notes'
    ];
    protected $casts = [
        'system_stock' => 'decimal:2',
        'actual_stock' => 'decimal:2',
    ];

    public function opname()     { return $this->belongsTo(StockOpname::class,'opname_id'); }
    public function ingredient() { return $this->belongsTo(Ingredient::class); }

    public function getDifferenceAttribute(): float
    {
        return (float)$this->actual_stock - (float)$this->system_stock;
    }
}
