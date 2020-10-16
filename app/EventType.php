<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
   const EVENT_SCHEDULED = 1;
   const TEMPORARY = 2;
}
