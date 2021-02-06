<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PaymentTypes extends Model
{
    public $table = 'payment_types';

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
