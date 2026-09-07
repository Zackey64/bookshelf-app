<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_ジャンル一覧を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        Genre::factory()->count(3)->create();
        // Act
        $response = $this->actingAs($user)->get(route('genres.index'));
        // Assert
        $response->assertOk()->assertViewIs('genres.index')->assertViewHas('genres');
    }

    /** @test */
    public function index_ジャンルが0件の場合も表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('genres.index'));
        // Assert
        $response->assertOk()->assertViewIs('genres.index')->assertViewHas('genres');
    }

    /** @test */
    public function create_ジャンル登録画面を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('genres.create'));
        // Assert
        $response->assertOk()->assertViewIs('genres.create');
    }

    /** @test */
    public function store_ジャンルを登録できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $data = [
            'name' => 'テストジャンル',
        ];
        // Act
        $response = $this->actingAs($user)->post(route('genres.store'), $data);
        // Assert
        $response->assertRedirect(route('genres.index'));
        $this->assertDatabaseHas('genres', [
            'name' => 'テストジャンル',
        ]);
    }

    /** @test */
    public function show_ジャンル詳細を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('genres.show', $genre));
        // Assert
        $response->assertOk()->assertViewIs('genres.show')->assertViewHas('genre', $genre);
    }

    /** @test */
    public function show_存在しないジャンルの場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('genres.show', 99999));
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function edit_ジャンル編集画面を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('genres.edit', $genre));
        // Assert
        $response->assertOk()->assertViewIs('genres.edit')->assertViewHas('genre', $genre);
    }

    /** @test */
    public function edit_存在しないジャンルの場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('genres.edit', 99999));
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function update_ジャンルを更新できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $genre = Genre::factory()->create([
            'name' => '更新前のジャンル',
        ]);
        $data = [
            'name' => '更新後のジャンル',
        ];
        // Act
        $response = $this->actingAs($user)->put(route('genres.update', $genre), $data);
        // Assert
        $response->assertRedirect(route('genres.index'));

        $this->assertDatabaseHas('genres', [
            'name' => '更新後のジャンル',
        ]);
    }

    /** @test */
    public function update_存在しないジャンルの場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->put(route('genres.update', 99999), []);
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function destroy_ジャンルを削除できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $genre = Genre::factory()->create([
            'name' => 'テストジャンル',
        ]);
        // Act
        $response = $this->actingAs($user)->delete(route('genres.destroy', $genre));
        // Assert
        $response->assertRedirect(route('genres.index'));
        $this->assertDatabaseMissing('genres', [
            'name' => 'テストジャンル',
        ]);
    }

    /** @test */
    public function destroy_存在しないジャンルの場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->delete(route('genres.destroy', 99999));
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function destroy_書籍が紐づいているジャンルは削除できない(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $genre = Genre::factory()->create([
            'name' => 'テストジャンル',
        ]);
        $book->genres()->attach($genre);
        // Act
        $response = $this->actingAs($user)->delete(route('genres.destroy', $genre));
        // Assert
        $this->assertDatabaseHas('genres', [
            'name' => 'テストジャンル',
        ]);
    }
}
