<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'truck_id', 'user_id', 'location_id', 'start_date_time', 'end_date_time', 'event_type_id', 'deleted'
    ];

    public function location()
    {
        return $this->belongsTo('App\Location');
    }

    public function truck()
    {
        return $this->belongsTo('App\Truck');
    }
}
