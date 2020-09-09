<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class LocationCache extends Model
{
    protected $table = 'location_cache';

    protected $fillable = [
        'name', 'formatted_address', 'payload', 'latitude', 'longitude'
    ];

}
