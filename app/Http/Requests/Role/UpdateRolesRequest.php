<?php

namespace App\Http\Requests\Role;

use App\Helpers\RoleHelper;
use App\Traits\UsesRequestValidation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRolesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    use UsesRequestValidation;
    public function authorize()
    {
        RoleHelper::need('role_edit');
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
            'id' => [
                'required',
            ],
            'name' => [
                'required',
            ],
            'permissions'   => [
                'required',
                'array',
            ],
        ];
    }
}
