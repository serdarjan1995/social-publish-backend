<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'user_roles';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'role_id',
    ];

    public function setUserRole($role_id)
    {
        $this->attributes['role_id'] = $role_id;
    }
}
