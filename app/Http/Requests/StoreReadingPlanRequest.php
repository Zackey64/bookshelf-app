<?php

namespace App\Http\Requests;

use App\Enums\ReadingPlanStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReadingPlanRequest extends FormRequest
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
            'book_id' => ['required', 'integer', 'exists:books,id',
                Rule::unique('reading_plans', 'book_id')->where(
                    function ($query) {
                        $query->where('user_id', $this->user()->id)->where('status', [
                            ReadingPlanStatus::InProgress->value,
                            ReadingPlanStatus::Expired->value,
                        ]);
                    }
                ),
            ],
            'target_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    //
    public function messages(): array
    {
        return [
            'book_id.required' => '書籍は必須です。',
            'book_id.exists' => '選択した書籍が存在しません。',
            'book_id.unique' => 'この書籍は既に登録されています。',

            'target_date.required' => '期日は必須です。',
            'target_date.date' => '正しい日付の形式で入力してください。',
            'target_date.after_or_equal' => '今日以降の日付で入力してください。',
        ];
    }
}
