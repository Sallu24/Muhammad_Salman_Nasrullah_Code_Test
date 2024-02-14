<?php

namespace DTApi\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_STARTED = 'started';
}
