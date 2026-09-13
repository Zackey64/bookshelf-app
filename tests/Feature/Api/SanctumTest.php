<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SanctumTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function store_sanctum認証済みユーザーは書籍を追加できる(): void
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
    public function store_未認証ユーザーは書籍を追加できない(): void
    {
        // Arrange
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
        $response->assertUnauthorized();
    }

    /** @test */
    public function update_sanctum認証済みユーザーは書籍を編集できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $genre = Genre::factory()->create();
        $data = [
            'title' => '更新後の書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890123',
            'genres' => [$genre->id],
        ];
        // Act
        $response = $this->putJson("/api/v1/books/{$book->id}", $data);
        // Assert
        $response->assertOk()->assertJsonPath('data.title', '更新後の書籍');
        $this->assertDatabaseHas('books', [
            'title' => '更新後の書籍',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function update_未認証ユーザーは書籍を編集できない(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $genre = Genre::factory()->create();
        $data = [
            'title' => '更新後の書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890123',
            'genres' => [$genre->id],
        ];
        // Act
        $response = $this->putJson("/api/v1/books/{$book->id}", $data);
        // Assert
        $response->assertUnauthorized();
    }

    /** @test */
    public function destroy_sanctum認証済みユーザーは自分の書籍を削除できる(): void
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
    public function destroy_未認証ユーザーは書籍を削除できない(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        // Act
        $response = $this->deleteJson("/api/v1/books/{$book->id}");
        // Assert
        $response->assertUnauthorized();
    }
}
