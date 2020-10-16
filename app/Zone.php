<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Zone extends Model
{
    public function timezone()
    {
        return $this->belongsTo('App\Timezone', 'zone_id', 'zone_id')->where('time_start', '<=', DB::raw('UNIX_TIMESTAMP(UTC_TIMESTAMP())'))->orderBy('time_start', 'DESC');
    }

}
