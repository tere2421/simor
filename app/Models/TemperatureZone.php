<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemperatureZone extends Model
{
    protected $fillable = ['name', 'location', 'min_temp', 'max_temp', 'description'];

    public function records() {
        return $this->hasMany(TemperatureRecord::class, 'zone_id');
    }

    public function latestRecord() {
        return $this->hasOne(TemperatureRecord::class, 'zone_id')->latestOfMany('recorded_at');
    }
}
