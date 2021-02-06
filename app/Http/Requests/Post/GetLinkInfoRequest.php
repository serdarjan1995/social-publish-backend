<?php

namespace App\Http\Requests\Post;

use App\Helpers\RoleHelper;
use App\Traits\UsesRequestValidation;
use Illuminate\Foundation\Http\FormRequest;

class GetLinkInfoRequest extends FormRequest
{
    use UsesRequestValidation;

    /**
     * @var mixed
     */
    private $url;

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
        return [
            'url' => [
                'required',
                'url'
            ],
        ];
    }
}
