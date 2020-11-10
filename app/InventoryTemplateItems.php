<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryTemplateItems extends Model
{
    protected $fillable = [
        'inventory_template_id', 'item_id', 'stock'
    ];
}
