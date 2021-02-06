<?php

namespace App\Model\AccountManager;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountCategory extends Model
{

    public $table = 'account_category';

    protected $fillable = [
        'social_network_id',
        'category',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected function serializeDate(DateTimeInterface $date){
        return $date->format('Y-m-d H:i:s');
    }
}
