<?php
namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PlanHelper
{
    static function need(string $permission)
    {
        if(Gate::denies($permission)){
            throw new HttpException(Response::HTTP_FORBIDDEN);
        }
    }

}
