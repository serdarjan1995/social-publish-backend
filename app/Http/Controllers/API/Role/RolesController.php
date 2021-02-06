<?php

namespace App\Http\Controllers\API\Role;

use App\Helpers\RoleHelper;
use App\Model\Role;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Role\StoreRolesRequest;
use App\Http\Requests\Role\UpdateRolesRequest;
use App\Http\Requests\Role\DestroyRolesRequest;


class RolesController extends ApiController
{
    public function index()
    {
        RoleHelper::need('role_access');
        return $this->success('',['roles' => Role::with(['permissions'])->get()]);
    }

    public function show($id)
    {
        RoleHelper::need('role_show');
        return $this->success('',['roles' => Role::with(['permission'])->findOrFail($id)]);
    }

    public function store(StoreRolesRequest $request)
    {
        $role = Role::create($request->all());

        $role->permissions()->sync($request->input('permissions', []));

        return $this->success(null);

    }

    public function update(UpdateRolesRequest $request)
    {
        $role = Role::findOrFail($request->id);
        $role->update($request->all());
        $role->permission()->sync($request->input('permission', []));
        return $this->success(null);
    }

    public function destroy(DestroyRolesRequest $request)
    {
        $role = Role::where('id',$request->only('id'))->first();
        if (!$role){
            return $this->fail(trans('role.role_error_delete'));
        }
        else{
            $role->delete();
            return $this->success(trans('role.role_delete'));
        }
    }
}
