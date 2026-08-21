<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $users = User::all();
        $books = Book::all();

        // 5人のユーザーが11冊の書籍に対してレビューを投稿（2〜4件のレビュー）
        // 最低２件振り分け（２２件）
        foreach ($books as $book) {
            $selectedUsers = $users->random(2);
            foreach ($selectedUsers as $user) {
                Review::factory()->create([
                    'book_id' => $book->id,
                    'user_id' => $user->id,
                    'rating' => rand(3, 5),
                ]);
            }
        }
        // 残りを０～２件振り分け（１０件）
        while (Review::count() < 32) {
            $selectedBook = $books->random();
            if ($selectedBook->reviews()->count() >= 4) {
                continue;
            }
            $selectedUsers = $users->random();
            Review::factory()->create([
                'book_id' => $selectedBook->id,
                'user_id' => $selectedUsers->id,
                'rating' => rand(3, 5),
            ]);
        }
    }
}
