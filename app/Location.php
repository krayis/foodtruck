<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/*
 * 1 - set
 * 1 - set
 * 3 - temp location
 */
class Location extends Model
{
    protected $fillable = [
        'truck_id', 'user_id', 'name', 'formatted_address', 'note', 'payload', 'latitude', 'longitude', 'geohash', 'deleted', 'location_type_id'
    ];
}
