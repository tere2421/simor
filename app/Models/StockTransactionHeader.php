<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransactionHeader extends Model
{
    protected $fillable = [
        'transaction_code', 'user_id', 'type', 'transaction_date', 'notes'
    ];

    protected $casts = ['transaction_date' => 'date'];

    public function user()  { return $this->belongsTo(User::class); }
    public function lines() { return $this->hasMany(StockTransaction::class, 'header_id'); }

    public function totalItems(): int
    {
        return $this->lines()->count();
    }

    /**
     * Generate kode transaksi otomatis: TRX-YYYYMMDD-XXX
     */
    public static function generateCode(string $type): string
    {
        $prefix  = $type === 'in' ? 'IN' : 'OUT';
        $date    = now()->format('Ymd');
        $base    = "{$prefix}-{$date}-";
        $last    = static::where('transaction_code', 'like', $base . '%')
                         ->orderByDesc('transaction_code')
                         ->value('transaction_code');
        $seq     = $last ? (intval(substr($last, -3)) + 1) : 1;
        return $base . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }
}
