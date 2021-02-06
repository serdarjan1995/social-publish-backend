<?php

namespace App\Http\Requests\Post;

use App\Helpers\RoleHelper;
use App\Traits\UsesRequestValidation;
use Illuminate\Foundation\Http\FormRequest;

class TextTemplateUpdateRequest extends FormRequest
{
    use UsesRequestValidation;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        RoleHelper::need('text_note_update');
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
            'id'=> [
                'required'
            ],
            'type'=> [
                'string'
            ],
            'title'=> [
                'required',
                'string'
            ],
            'description'=> [
                'required',
                'string'
            ],
            'tags'=> [
                'string',
            ],
        ];
    }
}
