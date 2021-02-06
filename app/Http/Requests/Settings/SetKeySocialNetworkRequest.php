<?php

namespace App\Http\Requests\Settings;

use App\Helpers\RoleHelper;
use App\Traits\UsesRequestValidation;
use Illuminate\Foundation\Http\FormRequest;

class SetKeySocialNetworkRequest extends FormRequest
{
    use UsesRequestValidation;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        RoleHelper::need('social_media_api_set');
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
            'social_network_id' => [
                'required',
                'integer'
            ],
            'api_key' => [
                'required',
                'string'
            ],
            'api_secret' => [
                'required',
                'string'
            ],
            'api_callback_url' => [
                'required',
                'string'
            ],
            'extra_settings' => [
                'required',
                'string'
            ]
        ];
    }
}
