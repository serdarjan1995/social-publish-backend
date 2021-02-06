<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $fillable = [
        'api_key', 'api_secret','extra_settings','social_network_id',
    ];
}
