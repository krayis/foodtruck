<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Item extends Model
{
    protected $fillable = [
        'truck_id', 'user_id', 'category_id', 'name', 'price', 'thumbnail', 'description', 'active', 'sort_order', 'deleted'
    ];

    public function category()
    {
        return $this->belongsTo('App\MenuCategory');
    }

    public function truck()
    {
        return $this->belongsTo('App\Truck');
    }

    public function modifierCategories()
    {
        return $this->belongsToMany('App\ModifierCategory', 'App\ItemModifierCategory');
    }

    public function modifiers()
    {
        return $this->hasManyThrough('App\Modifier', 'App\ItemModifierCategory', 'item_id', 'modifier_category_id', 'id', 'modifier_category_id');
    }

}
