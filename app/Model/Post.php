<?php

namespace App\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use DateTimeInterface;

class Post extends Model
{
    use SoftDeletes;

    protected $table = "posts";
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $casts = [ 'post_data' => 'array', 'post_schedule' => 'array' ];



    protected $fillable = [
        'account_id',
        'user_id',
        'post_id',
        'post_caption',
        'post_data',
        'post_type',
        'post_schedule',
        'post_status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
