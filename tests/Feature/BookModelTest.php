<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\ReadingPlan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_この書籍を登録したユーザーと１対多の関係を持つ(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        // Assert
        $this->assertTrue($book->user->is($user));
    }

    /** @test */
    public function genres_ジャンルと多対多の関係を持つ(): void
    {
        // Arrange
        $book = Book::factory()->create();
        $genre = Genre::factory()->create();
        $book->genres()->attach($genre);
        // Assert
        $this->assertTrue($book->genres->contains($genre));
    }

    /** @test */
    public function reviews_レビューと１対多の関係を持つ(): void
    {
        // Arrange
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);
        // Assert
        $this->assertTrue($book->reviews->contains($review));
    }

    /** @test */
    public function favorited_by_users_書籍をお気に入り登録しているユーザーと多対多の関係を持つ(): void
    {
        // Arrange
        $book = Book::factory()->create();
        $user = User::factory()->create();
        $book->favoritedByUsers()->attach($user);
        // Assert
        $this->assertTrue($book->favoritedByUsers->contains($user));
    }

    // 【応用追加】

    /** @test */
    public function reading_plans_読書計画と１対多の関係を持つ(): void
    {
        // Arrange
        $book = Book::factory()->create();
        $readingPlan = ReadingPlan::factory()->create([
            'book_id' => $book->id,
        ]);
        // Assert
        $this->assertTrue($book->readingPlans->contains($readingPlan));
    }
}
