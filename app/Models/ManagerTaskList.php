<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ManagerTaskList extends Model
{
    protected $fillable = [
        'title','url','role_target','frequency','category','order','is_active','created_by'
    ];
    protected $casts = ['is_active' => 'boolean'];

    public function records()   { return $this->hasMany(ManagerTaskRecord::class,'task_id'); }
    public function creator()   { return $this->belongsTo(User::class,'created_by'); }

    // Label frekuensi dalam bahasa Indonesia
    public function frequencyLabel(): string
    {
        return match($this->frequency) {
            'daily'     => 'Setiap Hari',
            'monday'    => 'Setiap Senin',
            'tuesday'   => 'Setiap Selasa',
            'wednesday' => 'Setiap Rabu',
            'thursday'  => 'Setiap Kamis',
            'friday'    => 'Setiap Jumat',
            'weekly'    => 'Mingguan',
            'monthly'   => 'Bulanan',
            default     => $this->frequency,
        };
    }

    // Apakah task ini aktif untuk hari ini?
    public function isActiveToday(): bool
    {
        if (!$this->is_active) return false;
        $day = strtolower(now()->locale('en')->dayName);
        return match($this->frequency) {
            'daily'                                       => true,
            'monday','tuesday','wednesday','thursday','friday' => $this->frequency === $day,
            'weekly'                                      => $day === 'monday',
            'monthly'                                     => now()->day === 1,
            default                                       => false,
        };
    }
}
