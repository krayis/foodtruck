<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModifierCategory extends Model
{
    protected $fillable = [
        'truck_id', 'user_id', 'name', 'min', 'max', 'modifier_category_type_id', 'deleted', 'active'
    ];

    public function modifiers()
    {
        return $this->hasMany('App\Modifier')->where('deleted', 0)->orderBy('sort_order', 'desc');
    }
}
