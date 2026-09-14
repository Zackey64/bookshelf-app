<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class IndexBookRequest extends FormRequest
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
            'keyword' => ['nullable', 'string'],
            'genre' => ['nullable', 'integer', 'exists:genres,id'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
