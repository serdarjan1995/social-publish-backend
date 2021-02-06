<?php

namespace App\Model;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use UsesUuid;

    protected $fillable = [
        'user_id',
        'send_id',
        'message',
        'extra_details',
        'type'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
