<?php

namespace App\Http\Requests\Auth;

use App\Traits\UsesRequestValidation;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'token' => 'required|string',
            'password' => 'required|string',
            'password_confirmation' => 'required|string'
        ];
    }
}
