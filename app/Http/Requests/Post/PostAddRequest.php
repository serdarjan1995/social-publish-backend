<?php

namespace App\Http\Requests\Post;

use App\Helpers\RoleHelper;
use App\Traits\UsesRequestValidation;
use Illuminate\Foundation\Http\FormRequest;

class PostAddRequest extends FormRequest
{
    use UsesRequestValidation;

    /**
     * @var mixed
     */
    private $post_schedule;
    /**
     * @var mixed
     */
    private $account_ids;
    /**
     * @var mixed
     */
    private $post_caption;
    /**
     * @var mixed
     */
    private $post_type;
    /**
     * @var mixed
     */
    private $post_data;
    /**
     * @var mixed
     */
    private $post_title;

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
            'account_ids' => [
                'required',
                'array'
            ],
            'post_type' => [
                'required',
                'string'
            ],
            'post_caption' => [
                'string'
            ],
            'post_title' => [
                'string'
            ],
            'post_data' => [
                'array'
            ]
        ];
    }
}
