<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'staff_profile_id', 'schedule_id', 'date',
        'check_in', 'check_out',
        'check_in_actual', 'late_minutes',
        'status', 'notes', 'problem_description',
        'recorded_by',   // ← siapa yang mencatat
    ];

    protected $casts = ['date' => 'date'];

    public function staffProfile() { return $this->belongsTo(StaffProfile::class); }
    public function schedule()     { return $this->belongsTo(Schedule::class); }
    public function recorder()     { return $this->belongsTo(User::class, 'recorded_by'); }  // ← pencatat

    public static function statusLabel(string $status): string
    {
        return match($status) {
            'terlambat'    => '⏰ Terlambat',
            'alpha'        => '❌ Alpha',
            'tidak_hadir'  => '🚫 Tidak Hadir',
            'izin'         => '📋 Izin',
            'sakit'        => '🤒 Sakit',
            'pulang_awal'  => '🏃 Pulang Awal',
            'masalah_lain' => '⚠️ Masalah Lain',
            default        => $status,
        };
    }

    public static function statusColor(string $status): string
    {
        return match($status) {
            'terlambat'    => 'badge-warning',
            'alpha'        => 'badge-danger',
            'tidak_hadir'  => 'badge-danger',
            'izin'         => 'badge-info',
            'sakit'        => 'badge-warning',
            'pulang_awal'  => 'badge-warning',
            'masalah_lain' => 'badge-danger',
            default        => 'badge-gray',
        };
    }
}
