<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function store_レビューを登録できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $data = [
            'rating' => 5,
            'comment' => 'テストコメント',
        ];
        // Act
        $response = $this->actingAs($user)->post(route('reviews.store', $book), $data);
        // Assert
        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseHas('reviews', [
            'comment' => 'テストコメント',
        ]);
    }

    /** @test */
    public function edit_レビュー編集画面を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
        ]);
        // Act
        $response = $this->actingAs($user)->get(route('reviews.edit', $review));
        // Assert
        $response->assertOk()->assertViewIs('reviews.edit')->assertViewHas('review', $review);
    }

    /** @test */
    public function edit_存在しないレビューの場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('reviews.edit', 99999));
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function update_レビューを更新できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'comment' => '更新前のコメント',
        ]);
        $data = [
            'rating' => 5,
            'comment' => '更新後のコメント',
        ];
        // Act
        $response = $this->actingAs($user)->put(route('reviews.update', [$book, $review]), $data);
        // Assert
        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseHas('reviews', [
            'comment' => '更新後のコメント',
        ]);
    }

    /** @test */
    public function update_存在しないレビューの場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->put(route('reviews.update', 99999), []);
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function destroy_レビューを削除できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'comment' => 'テストコメント',
        ]);
        // Act
        $response = $this->actingAs($user)->delete(route('reviews.destroy', [$book, $review]));
        // Assert
        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseMissing('reviews', [
            'comment' => 'テストコメント',
        ]);
    }

    /** @test */
    public function destroy_存在しないレビューの場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->delete(route('reviews.destroy', 99999));
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function like_いいねしてないときはいいねする(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);
        // Act
        $response = $this->actingAs($user)->post(route('reviews.like', [$book, $review]));
        // Assert
        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseHas('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);

    }

    /** @test */
    public function like_いいねしているときはいいねを解除する(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);
        DB::table('review_likes')->insert([
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
        // Act
        $response = $this->actingAs($user)->post(route('reviews.like', [$book, $review]));
        // Assert
        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseMissing('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);

    }
}
