<?php

namespace App\Http\Requests\Plan;

use App\Helpers\PlanHelper;
use App\Traits\UsesRequestValidation;
use Illuminate\Foundation\Http\FormRequest;

class DestroyPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    use UsesRequestValidation;
    public function authorize()
    {
        PlanHelper::need('plan_delete');
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
            'id' => [
                'required',
            ],
        ];
    }
}
