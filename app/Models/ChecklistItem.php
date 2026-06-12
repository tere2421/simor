<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    protected $fillable = ['name', 'description', 'order', 'shift', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function records() { return $this->hasMany(ChecklistRecord::class); }

    /**
     * Scope: ambil item yang berlaku untuk shift tertentu
     * (item dengan shift == $shift ATAU shift == 'all')
     */
    public function scopeForShift($query, string $shift)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) use ($shift) {
                         $q->where('shift', $shift)->orWhere('shift', 'all');
                     })
                     ->orderBy('shift')
                     ->orderBy('order');
    }
}
