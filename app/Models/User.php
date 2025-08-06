<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;

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

    public function rents()
    {
        return $this->hasMany(Rent::class, 'user_id', 'id');
    }

    public function latestRent()
    {
        return $this->rents()->orderByDesc('end_date')->first();
    }

    public function getStatusAttribute()
    {
        $latestRent = $this->latestRent();


        if (!$latestRent) {
            return null;
        }

        $seconds = now()->diffInSeconds(Carbon::parse($latestRent->end_date), false);

        return $seconds;
    }

    public function getAmountAttribute()
    {
        $isActice = $this->getStatusAttribute();
        if ($isActice == null or $isActice < 0) {
            return null;
        }

        $now = Carbon::now();

        $currentRent = $this->rents()
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->first();

        return $currentRent?->amount;
    }

    public function nextRentStartDate()
    {
        $latest = $this->latestRent();

        if (!$latest || Carbon::parse($latest->end_date)->lt(now())) {
            return now();
        }

        return Carbon::parse($latest->end_date)->copy()->addSecond();
    }
}
