<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModifierGroup extends Model
{
    protected $fillable = [
        'truck_id', 'user_id', 'name', 'has_custom_range', 'has_max_permitted', 'min_permitted', 'max_permitted', 'max_permitted_per_option', 'rule_condition', 'sort_order', 'deleted', 'active'
    ];

    public function modifiers()
    {
        return $this->hasMany('App\Modifier')->orderBy('sort_order', 'asc');
    }
}
