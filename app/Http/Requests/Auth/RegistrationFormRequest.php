<?php

namespace App\Http\Requests\Auth;
use App\Traits\UsesRequestValidation;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationFormRequest extends FormRequest
{
    use UsesRequestValidation;

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
        return  [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|string|min:8|max:15'
        ];
    }

    public function getAttributes() {
        return $this->validated();
    }

}
