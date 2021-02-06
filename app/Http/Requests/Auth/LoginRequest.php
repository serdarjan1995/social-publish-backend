<?php

namespace App\Http\Requests\Auth;

use App\Traits\UsesRequestValidation;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    use UsesRequestValidation;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string|max:25'
        ];
    }

}
