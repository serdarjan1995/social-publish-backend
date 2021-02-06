<?php

namespace App\Model;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class ProxyManager extends Model
{
    public $table = 'proxy_manager';

    protected $fillable = [
        'proxy_name',
        'proxy_location_code',
        'proxy_location_name',
        'proxy_limit',
        'status'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected function serializeDate(DateTimeInterface $date){
        return $date->format('Y-m-d H:i:s');
    }
}
