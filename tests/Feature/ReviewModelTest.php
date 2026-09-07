<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_レビューを投稿したユーザーと１対多の関係を持つ(): void
    {
        // Arrange
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
        ]);
        // Assert
        $this->assertTrue($review->user->is($user));
    }

    /** @test */
    public function book_レビューが投稿された書籍と１対多の関係を持つ(): void
    {
        // Arrange
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);
        // Assert
        $this->assertTrue($review->book->is($book));
    }

    /** @test */
    public function liked_by_users_レビューに対していいねしたユーザーと多対多の関係を持つ(): void
    {
        // Arrange
        $review = Review::factory()->create();
        $user = User::factory()->create();
        $review->likedByUsers()->attach($user);
        // Assert
        $this->assertTrue($review->likedByUsers->contains($user));
    }
}
