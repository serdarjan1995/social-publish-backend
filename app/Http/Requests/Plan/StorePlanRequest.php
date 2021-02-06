<?php


namespace App\Http\Requests\Plan;

use App\Helpers\PlanHelper;
use App\Traits\UsesRequestValidation;
use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    use UsesRequestValidation;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        PlanHelper::need('plan_create');
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
            'title' => [
                'required',
            ],
            'description' => [
                'required',
            ],
            'amount' => [
                'required',
            ],
        ];
    }
}
