<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function books_書籍と１対多の関係を持つ(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        // Assert
        $this->assertTrue($user->books->contains($book));
    }

    /** @test */
    public function reviews_レビューと１対多の関係を持つ(): void
    {
        // Arrange
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
        ]);
        // Assert
        $this->assertTrue($user->reviews->contains($review));
    }

    /** @test */
    public function favorite_books_お気に入りに登録した書籍と多対多の関係を持つ(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $user->favoriteBooks()->attach($book);
        // Assert
        $this->assertTrue($user->favoriteBooks->contains($book));
    }

    /** @test */
    public function liked_reviews_いいねしたレビューと多対多の関係を持つ(): void
    {
        // Arrange
        $user = User::factory()->create();
        $review = Review::factory()->create();
        $user->likedReviews()->attach($review);
        // Assert
        $this->assertTrue($user->likedReviews->contains($review));
    }

    // 【応用追加】

    /** @test */
    public function reading_plans_読書計画と１対多の関係を持つ(): void
    {
        // Arrange
        $user = User::factory()->create();
        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);
        // Assert
        $this->assertTrue($user->readingPlans->contains($readingPlan));
    }
}
