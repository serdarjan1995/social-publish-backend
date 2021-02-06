<?php


namespace App\Http\Requests\AccountManager;


use App\Traits\UsesRequestValidation;
use Illuminate\Foundation\Http\FormRequest;

class AddAccountFromCodeRequest extends FormRequest
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
            'code' => 'required',
            'social_network_id' => 'required|numeric',
        ];
    }
}
