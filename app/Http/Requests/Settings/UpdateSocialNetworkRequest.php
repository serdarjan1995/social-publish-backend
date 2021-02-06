<?php

namespace App\Http\Requests\Settings;

use App\Helpers\RoleHelper;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSocialNetworkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        RoleHelper::need('social_media_update');
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
            'name' => [
                'required',
                'string',
            ],
            'icon' => [
                'required',
                'string',
            ],
        ];
    }
}
