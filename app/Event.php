<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use DateTimeZone;
use DateTime;

class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'truck_id', 'user_id', 'location_id', 'start_date_time', 'end_date_time', 'type', 'preorder'
    ];

    public function location()
    {
        return $this->belongsTo('App\Location');
    }

    public function truck()
    {
        return $this->belongsTo('App\Truck');
    }

    public function startDateTime($tz) {
        $datetime = new DateTime($this->start_date_time);
        $timezone = new DateTimeZone($tz);
        $datetime->setTimezone($timezone);
        return $datetime;
    }

    public function endDateTime($tz) {
        $datetime = new DateTime($this->end_date_time);
        $timezone = new DateTimeZone($tz);
        $datetime->setTimezone($timezone);
        return $datetime;
    }
}
