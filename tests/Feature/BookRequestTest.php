<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function store_必須項目がない場合(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->post(route('books.store'), []);
        // Assert
        $response->assertSessionHasErrors([
            'title',
            'author',
            'isbn',
            'published_date',
            'genres',
        ]);
    }

    /** @test */
    public function store_他の書籍と同じ_isb_nの場合はエラー(): void
    {
        // Arrange
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();
        $data = [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => $book->isbn,
            'published_date' => '2026-09-01',
            'genres' => [$genre->id],
        ];
        // Act
        $response = $this->actingAs($user)->post(route('books.store'), $data);
        // Assert
        $response->assertSessionHasErrors(['isbn']);
    }

    /** @test */
    public function update_必須項目がない場合(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        // Act
        $response = $this->actingAs($user)
            ->put(route('books.update', $book), []);

        // Assert
        $response->assertSessionHasErrors([
            'title',
            'author',
            'isbn',
            'published_date',
            'genres',
        ]);
    }

    /** @test */
    public function update_他の書籍と同じ_isb_nの場合はエラー(): void
    {
        // Arrange
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $otherBook = Book::factory()->create();
        $data = [
            'title' => '更新後のテスト書籍',
            'author' => 'テスト著者',
            'isbn' => $otherBook->isbn,
            'published_date' => '2026-09-01',
            'genres' => [$genre->id],
        ];
        // Act
        $response = $this->actingAs($user)->put(route('books.update', $book), $data);
        // Assert
        $response->assertSessionHasErrors(['isbn']);
    }

    /** @test */
    public function update_元の書籍と同じ_isb_nの場合はエラーにならない(): void
    {
        // Arrange
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $data = [
            'title' => '更新後のテスト書籍',
            'author' => 'テスト著者',
            'isbn' => $book->isbn,
            'published_date' => '2026-09-01',
            'genres' => [$genre->id],
        ];
        // Act
        $response = $this->actingAs($user)->put(route('books.update', $book), $data);
        // Assert
        $response->assertSessionDoesntHaveErrors();
        $response->assertRedirect(route('books.index'));
    }
}
