<?php
namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class RoleHelper
{
    static function need(string $permission)
    {
        if(Gate::denies($permission)){
            throw new UnauthorizedHttpException(Response::HTTP_UNAUTHORIZED);
        }
    }

}
