<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const STATUS_CREATED = 1;
    const STATUS_PAID = 2;
    const STATUS_ACKNOWLEDGED = 3;
    const STATUS_READY= 4;
    const STATUS_FULFILLED = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'truck_id', 'event_id', 'uuid', 'tax', 'tip', 'sub_total', 'tax_total', 'grand_total', 'status', 'pickup_at'
    ];

    public function items()
    {
        return $this->hasMany('App\OrderItem');
    }

    public function truck()
    {
        return $this->belongsTo('App\Truck');
    }

}
