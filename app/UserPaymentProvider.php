<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPaymentProvider extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'email', 'minimum_order_amount', 'sales_tax', 'payment_provider'
    ];

}
