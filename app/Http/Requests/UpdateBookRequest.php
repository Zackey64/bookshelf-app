<?php

namespace App\Http\Requests;

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
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'isbn:13', Rule::unique('books', 'isbn')->ignore($bookId)],
            'published_date' => ['required', 'date'],
            'image_url' => ['nullable', 'string', 'url'],
            'description' => ['nullable', 'string', 'max:255'],
            //
            'genre_ids' => ['required', 'array'],
            'genre_ids.*' => ['integer', 'exists:genres,id', 'distinct'],
        ];
    }

    // バリテーションメッセージメソッド
    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'author.required' => '著者名は必須です。',
            'isbn.required' => 'ISBNは必須です。',
            'isbn.isbn' => '正しい13桁のISBNコードを入力してください。',
            'isbn.unique' => 'このISBNは既に登録されています。',
            'published_date.required' => '出版日は必須です。',
            'published_date.date' => '正しい日付の形式で入力してください。',
            'image_url.required' => '画像URLは必須です。',
            'image_url.url' => '正しいURL形式で入力してください。',
            'genre_ids.required' => 'ジャンルを最低1つ選択してください。',
            'genre_ids.*.exists' => '選択されたジャンルが正しくありません。',
        ];
    }
}
