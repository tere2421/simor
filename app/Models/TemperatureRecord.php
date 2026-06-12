<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemperatureRecord extends Model
{
    protected $fillable = ['zone_id', 'user_id', 'temperature', 'is_abnormal', 'notes', 'recorded_at'];

    protected $casts = [
        'temperature' => 'decimal:2',
        'is_abnormal' => 'boolean',
        'recorded_at' => 'datetime',
    ];

    protected static function booted() {
        static::creating(function ($record) {
            $zone = TemperatureZone::find($record->zone_id);
            if ($zone) {
                $record->is_abnormal = $record->temperature < $zone->min_temp
                                    || $record->temperature > $zone->max_temp;
            }
        });
    }

    public function zone() { return $this->belongsTo(TemperatureZone::class, 'zone_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
