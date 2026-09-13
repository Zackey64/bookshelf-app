<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function store_必須項目がない場合(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        // Act
        $response = $this->postJson('/api/v1/books', []);
        // Assert
        $response->assertUnprocessable()->assertJsonValidationErrors([
            'title',
            'author',
            'isbn',
            'genres',
        ]);
    }

    /** @test */
    public function update_必須項目がない場合(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        // Act
        $response = $this->putJson("/api/v1/books/{$book->id}", []);
        // Assert
        $response->assertUnprocessable()->assertJsonValidationErrors([
            'title',
            'author',
            'isbn',
            'genres',
        ]);
    }

    /** @test */
    public function update_元の書籍と同じ_isbnの場合はエラーにならない(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $genre = Genre::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $data = [
            'title' => '更新後のテスト書籍',
            'author' => 'テスト著者',
            'isbn' => $book->isbn,
            'genres' => [$genre->id],
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $data);

        $response->assertOk()->assertJsonMissingValidationErrors();
    }

    /** @test */
    public function update_他の書籍と同じ_isbnの場合はエラー(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $genre = Genre::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $otherBook = Book::factory()->create();

        $data = [
            'title' => '更新後のテスト書籍',
            'author' => 'テスト著者',
            'isbn' => $otherBook->isbn,
            'genres' => [$genre->id],
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $data);

        $response->assertUnprocessable()->assertJsonValidationErrors(['isbn']);
    }
}
