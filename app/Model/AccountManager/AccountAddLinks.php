<?php

namespace App\Model\AccountManager;

use Illuminate\Database\Eloquent\Model;

class AccountAddLinks extends Model
{
    public $table = 'account_add_links';

    protected $fillable = [
        'account_category_id',
        'type',
        'uri',
    ];
}
