<?php

namespace App\Http\Requests\Ticket;

use App\Helpers\RoleHelper;
use App\Traits\UsesRequestValidation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class AvailableTicketAdd extends FormRequest
{
    use UsesRequestValidation;

    public function authorize()
    {
        //RoleHelper::need('social_media_api_destroy');
        return true;
    }
    public function rules()
    {
        return [
            'ticket_id' => 'required',
            'message' => 'required',
        ];
    }

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
