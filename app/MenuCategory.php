<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class MenuCategory extends Model
{
    protected $fillable = [
        'truck_id', 'user_id', 'name', 'description', 'active', 'sort_order', 'deleted'
    ];

    public function items() {
        return $this->hasMany('App\Item', 'category_id')->orderBy('sort_order', 'asc');
    }
}
