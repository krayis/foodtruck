<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class PaymentProviders extends Model
{
    protected $fillable = [
        'truck_id', 'user_id', 'access_token', 'refresh_token', 'expires_at', 'merchant_id', 'vendor'
    ];
}
