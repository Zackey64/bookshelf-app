<?php

namespace App\Http\Requests;

use App\Enums\ReadingPlanStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReadingPlanRequest extends FormRequest
{
    /**
     * 読書計画を更新するリクエストを許可
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 読書計画更新時のバリデーションルールを取得
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'book_id' => ['required', 'integer', 'exists:books,id',
                Rule::unique('reading_plans', 'book_id')
                    ->ignore($this->route('readingPlan')->id)->where(
                        function ($query) {
                            $query->where('user_id', $this->user()->id)
                                ->where('status', ReadingPlanStatus::InProgress->value);
                        }
                    ),
            ],
            'target_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    /**
     * 読書計画更新時のバリデーションメッセージを取得
     *
     * @return array<string, array<int, mixed>>
     */
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
