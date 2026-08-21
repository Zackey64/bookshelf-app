<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $users = User::all();
        $books = Book::all();

        // 各ユーザーに3〜5冊のお気に入りを設定
        foreach ($users as $user) {
            $bookCount = rand(3, 5);
            // 書籍をランダムにピックアップ
            $favoriteBookIds = $books->random($bookCount)->pluck('id')->toArray();
            $user->favoriteBooks()->syncWithoutDetaching($favoriteBookIds);
        }

    }
}
