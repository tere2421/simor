<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistRecord extends Model
{
    protected $fillable = ['checklist_item_id', 'user_id', 'date', 'session', 'is_done', 'notes'];
    protected $casts = ['date' => 'date', 'is_done' => 'boolean'];

    public function item() { return $this->belongsTo(ChecklistItem::class, 'checklist_item_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
