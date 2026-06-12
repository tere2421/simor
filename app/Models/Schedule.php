<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'staff_profile_id', 'shift_id', 'schedule_date', 'status', 'week_number', 'notes', 'approved_by'
    ];

    protected $casts = ['schedule_date' => 'date'];

    public function staffProfile() { return $this->belongsTo(StaffProfile::class); }
    public function shift()        { return $this->belongsTo(Shift::class); }
    public function approvedBy()   { return $this->belongsTo(User::class, 'approved_by'); }
    public function attendance()   { return $this->hasOne(Attendance::class); }
}
