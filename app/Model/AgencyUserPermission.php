<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class AgencyUserPermission extends Model
{

    public $table = 'agency_user_permissions';

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'agency_parent_id',
        'agency_user_id',
        'account_id',
        'permissions'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
