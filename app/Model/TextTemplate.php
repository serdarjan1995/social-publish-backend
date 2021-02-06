<?php
namespace App\Model;

use DateTimeInterface;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TextTemplate extends Model
{
    use SoftDeletes;

    protected $table = "notes";

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'description',
        'tags',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected function serializeDate(DateTimeInterface $date){
        return $date->format('Y-m-d H:i:s');
    }
    protected $guarded = [];

}
