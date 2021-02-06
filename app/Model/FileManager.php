<?php

namespace App\Model;

use App\Traits\UsesUuid;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileManager extends Model
{
    use UsesUuid;
    use SoftDeletes;

    public $table = 'file_manager';

    protected $fillable = [
        'user_id',
        'name',
        'url',
        'lazy',
        'extension',
        'size',
        'type',
        'width',
        'height',
        'sub',
        'resource_type'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected function serializeDate(DateTimeInterface $date){
        return $date->format('Y-m-d H:i:s');
    }
}
