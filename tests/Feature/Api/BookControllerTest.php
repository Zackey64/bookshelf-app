<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_書籍一覧を取得できる(): void
    {
        // Arrange
        Book::factory()->count(3)->create();
        // Act
        $response = $this->getJson('/api/v1/books');
        // Assert
        $response->assertStatus(200);
        $response->assertOk()->assertJsonCount(3, 'data');
    }

    /** @test */
    public function index_書籍一覧が0件の場合は空配列を返す(): void
    {
        // Act
        $response = $this->getJson('/api/v1/books');
        // Assert
        $response->assertOk()->assertJsonCount(0, 'data');
        $response->assertOk()->assertJson(['data' => []]);
    }

    /** @test */
    public function index_検索できる(): void
    {
        // Arrange
        // Act
        // Assert
    }

    /** @test */
    public function show_書籍詳細を取得できる(): void
    {
        // Arrange
        $book = Book::factory()->create();
        // Act
        $response = $this->getJson("/api/v1/books/{$book->id}");
        // Assert
        $response->assertOk()->assertJsonPath('data.id', $book->id);
    }

    /** @test */
    public function show_存在しない書籍の場合はエラー(): void
    {
        // Act
        $response = $this->getJson('/api/v1/books/999');
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function store_書籍を追加できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $genre = Genre::factory()->create();
        $data = [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890123',
            'genres' => [$genre->id],
        ];
        // Act
        $response = $this->postJson('/api/v1/books', $data);
        // Assert
        $response->assertCreated()->assertJsonPath('data.title', 'テスト書籍');
        $this->assertDatabaseHas('books', [
            'title' => 'テスト書籍',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function update_書籍を編集できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $genre = Genre::factory()->create();
        $data = [
            'title' => '更新後のテスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890123',
            'genres' => [$genre->id],
        ];
        // Act
        $response = $this->putJson("/api/v1/books/{$book->id}", $data);
        // Assert
        $response->assertOk()->assertJsonPath('data.title', '更新後のテスト書籍');
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => '更新後のテスト書籍',
        ]);
    }

    /** @test */
    public function update_存在しない書籍の場合はエラー(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $genre = Genre::factory()->create();
        $data = [
            'title' => '更新後のテスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890123',
            'published_date' => '2026-09-01',
            'genres' => [$genre->id],
        ];
        // Act
        $response = $this->putJson('/api/v1/books/999', $data);
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function destroy_書籍を削除できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        // Act
        $response = $this->deleteJson("/api/v1/books/{$book->id}");
        // Assert
        $response->assertNoContent();
        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }

    /** @test */
    public function destroy_存在しない書籍の場合はエラー(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        // Act
        $response = $this->deleteJson('/api/v1/books/999');
        // Assert
        $response->assertNotFound();
    }
}
