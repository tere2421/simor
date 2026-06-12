<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffProfile extends Model
{
    protected $fillable = [
        'user_id', 'employee_id', 'name', 'position', 'shift_type', 'phone', 'join_date', 'is_active'
    ];

    protected $casts = ['join_date' => 'date', 'is_active' => 'boolean'];

    public function user()       { return $this->belongsTo(User::class); }
    public function schedules()  { return $this->hasMany(Schedule::class); }
    public function attendances(){ return $this->hasMany(Attendance::class); }
}
