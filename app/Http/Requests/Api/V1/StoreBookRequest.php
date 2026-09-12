<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    //
    public function authorize(): bool
    {
        return true;
    }

    // バリテーションルールメソッド
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'regex:/^[0-9]{13}$/', 'unique:books,isbn'],
            'published_date' => ['nullable', 'date'],
            'image_url' => ['nullable', 'url'],
            'description' => ['nullable', 'string', 'max:255'],
            //
            'genres' => ['required', 'array'],
            'genres.*' => ['integer', 'exists:genres,id', 'distinct'],
        ];
    }

    // バリテーションメッセージメソッド
    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'title.max' => 'タイトルは255文字以内で入力してください。',

            'author.required' => '著者名は必須です。',
            'author.max' => '著者名は255文字以内で入力してください。',

            'isbn.required' => 'ISBNは必須です。',
            'isbn.regex' => '13桁の数字で入力してください。',
            'isbn.unique' => 'このISBNは既に登録されています。',

            'published_date.date' => '正しい日付の形式で入力してください。',

            'image_url.url' => '正しいURL形式で入力してください。',

            'description.max' => '説明は255文字以内で入力してください。',

            'genres.required' => 'ジャンルは必須です。',
            'genres.*.exists' => '選択されたジャンルが正しくありません。',
        ];
    }
}
