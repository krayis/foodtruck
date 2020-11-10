<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventorySheetItems extends Model
{
    protected $fillable = [
        'inventory_sheet_id', 'stock', 'item_id'
    ];
}
