<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:255'],
        ];
    }

    //
    public function messages(): array
    {
        return [
            'rating.required' => '評価を選択してください。',
            'rating.integer' => '評価が正しくありません。',
            'rating.between' => '評価は1から5の間で選択してください。',
            'comment.required' => 'コメントを入力してください。',
            'comment.max' => 'コメントは255文字以内で入力してください。',
        ];
    }
}
