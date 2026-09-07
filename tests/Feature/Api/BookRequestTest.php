<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function store_必須項目がない場合(): void
    {
        // Act
        $response = $this->postJson('/api/books', []);
        // Assert
        $response->assertUnprocessable()->assertJsonValidationErrors([
            'user_id',
            'title',
            'author',
            'isbn',
            'published_date',
            'genres',
        ]);
    }

    /** @test */
    public function store_存在しないユーザーの場合(): void
    {
        // Arrange
        $data = [
            'user_id' => 999,
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890123',
            'published_date' => '2026-09-01',
            'genres' => [],
        ];
        // Act
        $response = $this->postJson('/api/books', $data);
        // Assert
        $response->assertUnprocessable()->assertJsonValidationErrors(['user_id']);
    }

    /** @test */
    public function update_自身のISBNはエラーにならない(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();
        $data = [
            'user_id' => $user->id,
            'title' => '更新後のテスト書籍',
            'author' => 'テスト著者',
            'isbn' => $book->isbn,
            'published_date' => '2026-09-01',
            'genres' => [$genre->id],
        ];

        $response = $this->putJson("/api/books/{$book->id}", $data);

        $response->assertOk()->assertJsonMissingValidationErrors();
    }

    /** @test */
    public function update_他の書籍と同じISBNの場合はエラー(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();
        $otherBook = Book::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title' => '更新後のテスト書籍',
            'author' => 'テスト著者',
            'isbn' => $otherBook->isbn,
            'published_date' => '2026-09-01',
            'genres' => [$genre->id],
        ];

        $response = $this->putJson("/api/books/{$book->id}", $data);

        $response->assertUnprocessable()->assertJsonValidationErrors(['isbn']);
    }




}
