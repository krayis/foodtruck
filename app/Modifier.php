<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modifier extends Model
{
    protected $fillable = [
        'truck_id', 'user_id', 'modifier_group_id', 'name', 'price', 'min', 'max', 'type', 'active', 'deleted', 'sort_order',
    ];

    public function group()
    {
        return $this->belongsTo('App\ModifierGroup', 'modifier_group_id');
    }

}
