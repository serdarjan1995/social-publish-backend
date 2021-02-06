<?php


namespace App\Http\Requests\AccountManager;


use App\Traits\UsesRequestValidation;
use Illuminate\Foundation\Http\FormRequest;

class GetTwitterAccessRequest extends FormRequest
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
            'user' => 'required',
            'oauth_token' => 'required',
            'oauth_verifier' => 'required',
            'social_network_id' => 'required|numeric',
        ];
    }
}
