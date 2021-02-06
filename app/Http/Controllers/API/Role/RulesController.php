<?php

namespace App\Http\Controllers\API\Role;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Rules\RuleResource;

class RulesController extends ApiController
{
    public function check()
    {
        $permissions = auth()->user()->roles()->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name');

        return $this->success(null,['rules'=> new RuleResource($permissions)]);
    }
}
