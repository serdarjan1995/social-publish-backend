<?php

namespace App\Http\Requests\AccountManager;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class GroupManagerCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'groupName' => 'required'
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
