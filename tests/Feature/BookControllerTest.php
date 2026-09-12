<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_書籍一覧を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        Book::factory()->count(3)->create();
        // Act
        $response = $this->actingAs($user)->get(route('books.index'));
        // Assert
        $response->assertOk()->assertViewIs('books.index')->assertViewHas('books');
    }

    /** @test */
    public function index_書籍が0件の場合も表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('books.index'));
        // Assert
        $response->assertOk()->assertViewIs('books.index')->assertViewHas('books');
    }

    /** @test */
    public function index_検索できる(): void
    {
        // Arrange
        // Act
        // Assert
    }

    /** @test */
    public function create_書籍登録画面を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        Genre::factory()->count(3)->create();
        // Act
        $response = $this->actingAs($user)->get(route('books.create'));
        // Assert
        $response->assertOk()->assertViewIs('books.create')->assertViewHas('genres');
    }

    /** @test */
    public function store_書籍を登録できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $data = [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890123',
            'published_date' => '2026-09-01',
            'genres' => [$genre->id],
        ];
        // Act
        $response = $this->actingAs($user)->post(route('books.store'), $data);
        $book = Book::where('title', 'テスト書籍')->first();
        // Assert
        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseHas('books', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function show_書籍詳細を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('books.show', $book));
        // Assert
        $response->assertOk()->assertViewIs('books.show')->assertViewHas('book', $book);
    }

    /** @test */
    public function show_存在しない書籍の場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('books.show', 99999));
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function edit_書籍編集画面を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        Genre::factory()->count(3)->create();
        // Act
        $response = $this->actingAs($user)->get(route('books.edit', $book));
        // Assert
        $response->assertOk()->assertViewIs('books.edit')->assertViewHas('book', $book)->assertViewHas('genres');
    }

    /** @test */
    public function edit_存在しない書籍の場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('books.edit', 99999));
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function update_書籍を更新できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
            'title' => '更新前の書籍',
        ]);
        $data = [
            'title' => '更新後の書籍',
            'author' => $book->author,
            'isbn' => $book->isbn,
            'published_date' => '2026-09-01',
            'genres' => [$genre->id],
        ];
        // Act
        $response = $this->actingAs($user)->put(route('books.update', $book), $data);
        // Assert
        $response->assertRedirect(route('books.show', $book));

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => '更新後の書籍',
        ]);
    }

    /** @test */
    public function update_存在しない書籍の場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->put(route('books.update', 99999), []);
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function destroy_書籍を削除できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        // Act
        $response = $this->actingAs($user)->delete(route('books.destroy', $book));
        // Assert
        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }

    /** @test */
    public function destroy_存在しない書籍の場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->delete(route('books.destroy', 99999));
        // Assert
        $response->assertNotFound();
    }
}
