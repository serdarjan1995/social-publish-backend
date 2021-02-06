<?php

namespace App\Model;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use UsesUuid;
    protected $fillable = [
        'user_id',
        'type_id',
        'package_name',
        'transaction_id',
        'plan',
        'amount',
        'status',
    ];

    public function paymentName(){
        return $this->hasOne('App\Model\PaymentTypes', 'id', 'type_id');
    }
}
