<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItemModifier extends Model
{
    protected $fillable = [
        'order_id', 'order_item_id', 'name', 'price',
    ];
}
