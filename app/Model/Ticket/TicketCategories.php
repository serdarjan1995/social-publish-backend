<?php

namespace App\Model\Ticket;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketCategories extends Model
{
    use SoftDeletes;

    public $table = 'ticket_categories';

    protected $fillable = [
        'category_name',
    ];

}
