<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Shift extends Model
{
    protected $fillable = ['code','name','start_time','end_time','duration_hours','description'];

    public function schedules() { return $this->hasMany(Schedule::class); }

    /**
     * Parse kode shift: H080800
     *  H        = prefix perusahaan (wajib)
     *  08       = durasi 8 jam
     *  0800     = jam masuk 08:00
     * → clock out = 08:00 + 8 jam = 16:00
     */
    public static function parseCode(string $code): array
    {
        // Validasi format: H + 2 digit durasi + 4 digit jam masuk
        if (!preg_match('/^H(\d{2})(\d{2})(\d{2})$/', $code, $m)) {
            throw new \InvalidArgumentException(
                "Format kode shift tidak valid. Harus: H + 2 digit durasi + 4 digit jam masuk. Contoh: H080800"
            );
        }

        $duration  = (int) $m[1];              // 08 → 8 jam
        $clockInH  = (int) $m[2];              // 08
        $clockInM  = (int) $m[3];              // 00
        $startTime = sprintf('%02d:%02d', $clockInH, $clockInM);

        // Hitung clock out
        $startDt   = Carbon::createFromFormat('H:i', $startTime);
        $endDt     = $startDt->copy()->addHours($duration);
        $endTime   = $endDt->format('H:i');

        // Nama otomatis: contoh "H080800 (08:00 – 16:00)"
        $name = "{$code} ({$startTime} – {$endTime})";

        return [
            'code'           => $code,
            'name'           => $name,
            'duration_hours' => $duration,
            'start_time'     => $startTime,
            'end_time'       => $endTime,
        ];
    }

    public function getClockOutAttribute(): string
    {
    return Carbon::parse($this->start_time)
        ->addHours($this->duration_hours ?? 8)
        ->format('H:i');
    }

    public function getLabelAttribute(): string
    {
        return "{$this->code} · {$this->start_time}–{$this->clock_out}";
    }
}
