<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    protected $fillable = [
        'opname_code','user_id','opname_date','status','period','notes','approved_by'
    ];
    protected $casts = ['opname_date' => 'date'];

    public function user()      { return $this->belongsTo(User::class); }
    public function approver()  { return $this->belongsTo(User::class,'approved_by'); }
    public function lines()     { return $this->hasMany(StockOpnameLine::class, 'opname_id'); }

    public static function generateCode(string $period): string
    {
        $base = 'OP-' . str_replace('-', '-', $period) . '-';
        $last = static::where('opname_code','like',$base.'%')->orderByDesc('opname_code')->value('opname_code');
        $seq  = $last ? intval(substr($last,-3))+1 : 1;
        return $base . str_pad($seq,3,'0',STR_PAD_LEFT);
    }
}
