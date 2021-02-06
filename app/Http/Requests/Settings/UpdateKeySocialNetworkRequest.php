<?php

namespace App\Http\Requests\Settings;

use App\Helpers\RoleHelper;
use Illuminate\Foundation\Http\FormRequest;

class UpdateKeySocialNetworkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        RoleHelper::need('social_media_api_update');
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
            'api_key'=> [
                'required',
                'string'
            ],
            'api_secret' => [
                'required',
                'string'
            ],
            'extra_settings' => [
                'required',
                'array'
            ],
        ];
    }
}
