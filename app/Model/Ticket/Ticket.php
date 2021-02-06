<?php


namespace App\Model\Ticket;


use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    public $table = 'tickets';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'name_surname',
        'category_id',
        'email',
        'created_user'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function message(){
        return $this->hasMany('App\Model\Ticket\TicketMessage', 'ticket_id', 'id');
    }
}
