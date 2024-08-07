<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\CreditTransaction;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'credits',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function creditTransactions()
    {
        return $this->hasMany(CreditTransaction::class);
    }

    public function hasExpiredCredits()
    {
        return $this->creditTransactions()
            ->where('expiry_date', '<', now())
            ->exists();
    }
}
