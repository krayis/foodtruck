<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Item extends Model
{
    protected $fillable = [
        'truck_id', 'user_id', 'category_id', 'name', 'price', 'thumbnail', 'description', 'active', 'sort_order', 'deleted', 'oos_until'
    ];

    public function category()
    {
        return $this->belongsTo('App\MenuCategory');
    }

    public function truck()
    {
        return $this->belongsTo('App\Truck');
    }

    public function modifierGroups()
    {
        return $this->belongsToMany('App\ModifierGroup', 'App\ItemModifierGroup')->orderBy('sort_order', 'asc');
    }

    public function modifiers()
    {
        return $this->hasManyThrough('App\Modifier', 'App\ItemModifierGroup', 'item_id', 'modifier_group_id', 'id', 'modifier_group_id');
    }

}
