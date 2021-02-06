<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

trait UsesRequestValidation
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()
            ->json([
                'status' => trans('api.failure'),
                'errors' => true,
                'locale' => app()->getLocale(),
                'data' => [
                    'message' => $validator->errors(),
                ]
            ],JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
