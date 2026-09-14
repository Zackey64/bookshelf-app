<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGenreRequest extends FormRequest
{
    /**
     * ジャンルを追加するリクエストを許可
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * ジャンル追加時のバリデーションルールを取得
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', 'unique:genres,name'],
        ];
    }

    /**
     * ジャンル追加時のバリデーションメッセージを取得
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
