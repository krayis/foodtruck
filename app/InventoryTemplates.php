<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryTemplates extends Model
{
    protected $fillable = [
        'user_id', 'truck_id', 'name'
    ];

    public function items()
    {
        return $this->hasMany('App\InventoryTemplateItems', 'inventory_template_id');
    }

}
