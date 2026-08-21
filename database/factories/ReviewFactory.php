<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'user_id' => User::factory(),
            'rating' => fake()->numberBetween(1, 5), // 1〜5のランダムな数値
            'comment' => fake()->randomElement([
                '素晴らしい本でした！', '人生が変わりました。', '何度も読み返しています。',
                'とても参考になりました。', '読みやすくておすすめです。', '期待通りの内容でした。',
                '普通でした。', '可もなく不可もなく。', '期待したほどではなかった。',
                '少し期待外れでした。', '内容が薄い印象。', 'もう少し深掘りしてほしかった。',
                '残念ながら合いませんでした。', '期待と違いました。',
            ]),
        ];
    }
}
