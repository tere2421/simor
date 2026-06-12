<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagerTaskRecord extends Model
{
    protected $fillable = ['task_id', 'user_id', 'date', 'is_done', 'notes'];
    protected $casts    = [
        'date'    => 'date',
        'is_done' => 'boolean',
    ];

    public function task() { return $this->belongsTo(ManagerTaskList::class, 'task_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
