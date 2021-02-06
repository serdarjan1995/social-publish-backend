<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialNetworkApi extends Model
{
    use SoftDeletes;
    public $table = 'api_keys';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'social_network_id',
        'api_key',
        'api_secret',
        'extra_settings',
        'api_callback_url',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
