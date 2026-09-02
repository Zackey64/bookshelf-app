<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
{
    //
    public function authorize(): bool
    {
        return true;
    }

    // バリテーションルールメソッド
    public function rules(): array
    {
        $bookId = $this->route('book');

        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            //
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['required',  'regex:/^[0-9]{13}$/', Rule::unique('books', 'isbn')->ignore($bookId)],
            'published_date' => ['required', 'date'],
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
            'user_id.required' => '登録者IDは必須です。',
            'user_id.exists' => '指定された登録者が存在しません。',

            'title.required' => 'タイトルは必須です。',
            'title.max' => 'タイトルは255文字以内で入力してください。',

            'author.required' => '著者名は必須です。',
            'author.max' => '著者名は255文字以内で入力してください。',

            'isbn.required' => 'ISBNは必須です。',
            'isbn.regex' => '13桁の数字で入力してください。',
            'isbn.unique' => 'このISBNは既に登録されています。',

            'published_date.required' => '出版日は必須です。',
            'published_date.date' => '正しい日付の形式で入力してください。',

            'image_url.url' => '正しいURL形式で入力してください。',

            'description.max' => '説明は255文字以内で入力してください。',

            'genres.required' => 'ジャンルは必須です。',
            'genres.*.exists' => '選択されたジャンルが正しくありません。',
        ];
    }
}
