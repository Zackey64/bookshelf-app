<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_お気に入り一覧を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $user->favoriteBooks()->attach($book->id);
        // Act
        $response = $this->actingAs($user)->get(route('favorites.index'));
        // Assert
        $response->assertOk()->assertViewIs('favorites.index')->assertViewHas('books');
    }

    /** @test */
    public function index_お気に入りが0件の場合も表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('favorites.index'));
        // Assert
        $response->assertOk()->assertViewIs('favorites.index')->assertViewHas('books');
    }

    /** @test */
    public function toggle_お気に入りしてないときは登録する(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();
        // Act
        $response = $this->actingAs($user)->from(route('books.show', $book))->post(route('favorites.toggle', $book));
        // Assert
        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

    }

    /** @test */
    public function toggle_お気に入りしているときは解除する(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $user->favoriteBooks()->attach($book->id);
        // Act
        $response = $this->actingAs($user)->from(route('books.show', $book))->post(route('favorites.toggle', $book));
        // Assert
        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

    }
}
