<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Timezone extends Model
{
    public function gmtOffset() {
        $offset = gmdate("h:i", abs($this->gmt_offset));
        $offset = $this->gmt_offset < 0 ? '-' . $offset : '+' . $offset;
        return $offset;
    }
}
