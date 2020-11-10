<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventorySheets extends Model
{
    protected $fillable = [
        'user_id', 'truck_id', 'event_id', 'name'
    ];

    public function items()
    {
        return $this->hasMany('App\InventorySheetItems', 'inventory_sheet_id');
    }
}
