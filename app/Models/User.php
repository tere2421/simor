<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }

    public function staffProfile() {
        return $this->hasOne(StaffProfile::class);
    }

    public function stockTransactions() {
        return $this->hasMany(StockTransaction::class);
    }

    public function temperatureRecords() {
        return $this->hasMany(TemperatureRecord::class);
    }

    public function isSM(): bool    { return $this->role === 'SM'; }
    public function isPIC(): bool   { return $this->role === 'PIC'; }
    public function isStaff(): bool { return $this->role === 'Staff'; }
    public function isManager(): bool { return in_array($this->role, ['SM', 'PIC']); }
}
