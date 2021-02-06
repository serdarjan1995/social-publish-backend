<?php

namespace App\Http\Requests\Post;

use App\Helpers\RoleHelper;
use App\Traits\UsesRequestValidation;
use Illuminate\Foundation\Http\FormRequest;

class PostIdRequiredRequest extends FormRequest
{
    use UsesRequestValidation;

    /**
     * @var mixed
     */
    private $post_id;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'post_id' => [
                'required',
                'string'
            ]
        ];
    }
}
