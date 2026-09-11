<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReadingPlanRequest extends FormRequest
{
    //
    public function authorize(): bool
    {
        return true;
    }

    //
    public function rules(): array
    {
        return [
            'target_date' => ['required', 'date'],
        ];
    }

    //
    public function messages(): array
    {
        return [
            'target_date.required' => '期日は必須です。',
            'target_date.date' => '正しい日付の形式で入力してください。',
        ];
    }
}
