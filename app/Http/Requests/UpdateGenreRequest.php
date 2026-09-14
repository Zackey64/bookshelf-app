<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGenreRequest extends FormRequest
{
    /**
     * ジャンルを更新するリクエストを許可
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * ジャンル更新時のバリデーションルールを取得
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $genreId = $this->route('genre');

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('genres', 'name')->ignore($genreId),
            ],
        ];
    }

    /**
     * ジャンル更新時のバリデーションメッセージを取得
     *
     * @return array<string, array<int, mixed>>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'ジャンル名は必須です。',
            'name.max' => 'ジャンル名は50文字以内で入力してください。',
            'name.unique' => 'このジャンル名は既に登録されています。',
        ];
    }
}
