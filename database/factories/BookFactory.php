<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->realText(20).'の本', // 20文字程度のタイトル風テキスト
            'author' => fake()->name(),
            'isbn' => fake()->numerify('9784#########'), // 9784から始まる13桁の数字
            'published_date' => fake()->date('Y-m-d', 'now'), // 今日までの過去の日付
            'description' => fake()->realText(100), // 100文字程度のリッチな説明文
            'image_url' => fake()->imageUrl(640, 480, 'books', true), // ダミー画像のURL
        ];
    }
}
