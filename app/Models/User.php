<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'super_id',
        'master_id',
        'admin_id',
        'user_id',
        'staff_for',
        'expired_at',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function super()
    {
        return $this->belongsTo(User::class, 'super_id');
    }

    public function master()
    {
        return $this->belongsTo(User::class, 'master_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }



    public function dices()
    {
        return $this->belongsToMany(Dice::class);
    }

    public function diceTables()
    {
        return $this->belongsToMany(DiceTable::class);
    }

    public function isExpired(): bool
    {
        return $this->expired_at && now()->gt($this->expired_at);
    }
}
