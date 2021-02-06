<?php

namespace App\Http\Requests\Role;

use App\Helpers\RoleHelper;
use Illuminate\Foundation\Http\FormRequest;

class ShowRolesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        RoleHelper::need('role_show');
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
