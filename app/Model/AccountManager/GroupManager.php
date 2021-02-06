<?php

namespace App\Model\AccountManager;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class GroupManager extends Model
{
    use UsesUuid;

    public $table = 'group_manager';

    protected $fillable = [
        'user_id',
        'list',
        'group_name'
    ];

    protected $casts = [
        'list' => 'array'
    ];
}
