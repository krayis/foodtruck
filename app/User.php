<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'mobile_phone', 'email', 'email_notification', 'timezone', 'password', 'created_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function truck()
    {
        return $this->hasOne('App\Truck');
    }

    public function paymentProvider()
    {
        return $this->hasOne('App\UserPaymentProvider');
    }

    public function zone() {
        return $this->hasOne('App\Zone', 'zone_name', 'timezone');
    }

    public function tz() {
        return $this->hasOneThrough('App\Timezone', 'App\Zone', 'zone_name', 'zone_id', 'timezone', 'zone_id')->where('time_start', '<=', DB::raw('UNIX_TIMESTAMP(UTC_TIMESTAMP())'))->orderBy('time_start', 'DESC');
    }
}
