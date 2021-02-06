<?php

namespace App\Http\Controllers\API\Role;

use App\Helpers\RoleHelper;
use App\Http\Resources\PermissionsResource;
use App\Model\Permission;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Role\StorePermissionsRequest;
use App\Http\Requests\Role\ShowPermissionsRequest;
use App\Http\Requests\Role\UpdatePermissionsRequest;
use App\Http\Requests\Role\DestroyPermissionsRequest;

class PermissionsController extends ApiController
{
    public function index()
    {
        RoleHelper::need('permission_access');
        $data = new PermissionsResource(Permission::paginate(25));
        return $this->success(null, ["permissions" => $data]);
    }

    public function show(ShowPermissionsRequest $request)
    {
        return new PermissionsResource(Permission::with([])->findOrFail($request->id));
    }

    public function store(StorePermissionsRequest $request)
    {
        $data = Permission::create($request->all());
        return $this->success(null, ["items" => $data]);
    }

    public function update(UpdatePermissionsRequest $request)
    {
        $permission = Permission::where('id',$request->only('id'))->first();
        if (!$permission){
            return $this->fail(trans('role.role_error_delete'));
        }
        else{
            $permission->update($request->all());
            return $this->success(null, ["items" => $permission]);
        }
    }

    public function destroy(DestroyPermissionsRequest $request)
    {
        $permission = Permission::where('id',$request->only('id'))->first();
        if (!$permission){
            return $this->fail(trans('role.permission_error_delete'));
        }
        else{
            $permission->delete();
            return $this->success(trans('role.permission_delete'));
        }
    }
}
