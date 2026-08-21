<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $users = User::all();
        $reviews = Review::all();


        foreach ($reviews as $review) {
            // 自分でない
            $likableUsers = $users->where('id', '!=', $review->user_id);
            // 
            $userCount = rand(0, 3);
            if ($userCount > 0) {
                    // ランダムにピックアップ
                    $likerUserIds = $likableUsers->random($userCount)->pluck('id')->toArray();
                    $review->likedByUsers()->syncWithoutDetaching($likerUserIds);
            }
        }
    }
}
