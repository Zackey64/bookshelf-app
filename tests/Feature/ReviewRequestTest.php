<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function store_必須項目がない場合(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();
        // Act
        $response = $this->actingAs($user)->post(route('reviews.store', $book), []);
        // Assert
        $response->assertSessionHasErrors([
            'rating',
            'comment',
        ]);
    }

    /** @test */
    public function store_commentが1000文字を超える場合はエラー(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // Act
        $response = $this->actingAs($user)
            ->post(route('reviews.store', $book), [
                'rating' => 5,
                'comment' => str_repeat('あ', 1001),
            ]);

        // Assert
        $response->assertSessionHasErrors(['comment']);
    }

    /** @test */
    public function update_必須項目がない場合(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
        // Act
        $response = $this->actingAs($user)->put(route('reviews.update', $review), []);
        // Assert
        $response->assertSessionHasErrors([
            'rating',
            'comment',
        ]);
    }

    /** @test */
    public function update_commentが1000文字を超える場合はエラー(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
        $data = [
            'rating' => 5,
            'comment' => str_repeat('あ', 1001),
        ];
        // Act
        $response = $this->actingAs($user)->put(route('reviews.update', $review), $data);
        // Assert
        $response->assertSessionHasErrors(['comment']);
    }
}
