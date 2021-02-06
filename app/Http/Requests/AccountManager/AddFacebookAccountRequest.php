<?php


namespace App\Http\Requests\AccountManager;


use App\Traits\UsesRequestValidation;
use Illuminate\Foundation\Http\FormRequest;

class AddFacebookAccountRequest extends FormRequest
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
            'parent_id' => 'required',
            'accounts' => 'required|array',
        ];
    }
}
