<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ItemModifierGroup extends Model
{
    protected $fillable = [
        'user_id', 'truck_id', 'name', 'formatted_address', 'note', 'payload', 'latitude', 'longitude', 'geohash', 'deleted', 'location_type_id'
    ];
}
