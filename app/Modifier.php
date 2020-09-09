<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Modifier extends Model
{
    protected $fillable = [
        'truck_id', 'user_id', 'modifier_category_id', 'name', 'price', 'min', 'max', 'type', 'active', 'deleted', 'sort_order',
    ];

    public function category()
    {
        return $this->belongsTo('App\ModifierCategory', 'modifier_category_id');
    }

}
