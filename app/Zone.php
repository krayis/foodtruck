<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Zone extends Model
{
    public function timezone()
    {
        return $this->belongsTo('App\Timezone');
    }
}
