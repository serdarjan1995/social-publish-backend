<?php


namespace App\Http\Requests\AccountManager;

use App\Traits\UsesRequestValidation;
use Illuminate\Foundation\Http\FormRequest;


class GetAddAccountLinkRequest  extends  FormRequest
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
            'social_network_id' => 'numeric|required',
            'category' => 'required',
        ];
    }

}
