<?php

namespace App\Http\Requests\Settings;

use App\Helpers\RoleHelper;
use Illuminate\Foundation\Http\FormRequest;

class DeleteKeySocialNetworkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        RoleHelper::need('social_media_api_destroy');
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
            //
        ];
    }
}
