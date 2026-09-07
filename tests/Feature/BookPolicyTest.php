<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function edit_本人のみ編集画面を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        //  Act
        $response = $this->actingAs($user)->get(route('books.edit', $book));
        // Assert
        $response->assertOk();
    }

    /** @test */
    public function edit_本人以外は編集画面を表示できない(): void
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        //  Act
        $response = $this->actingAs($user)->get(route('books.edit', $book));
        // Assert
        $response->assertForbidden();
    }

    /** @test */
    public function update_本人のみ更新できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $data = [
            'title' => '更新後の書籍',
            'author' => $book->author,
            'isbn' => $book->isbn,
            'published_date' => '2026-09-01',
            'genres' => [$genre->id],
        ];
        //  Act
        $response = $this->actingAs($user)->put(route('books.update', $book), $data);
        // Assert
        $response->assertRedirect(route('books.show', $book));
    }

    /** @test */
    public function update_本人以外は更新できない(): void
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        //  Act
        $response = $this->actingAs($user)->put(route('books.update', $book), []);
        // Assert
        $this->assertFalse($user->can('update', $book));
    }

    /** @test */
    public function destroy_本人のみ削除できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        //  Act
        $response = $this->actingAs($user)->delete(route('books.destroy', $book));
        // Assert
        $response->assertRedirect(route('books.index'));
    }

    /** @test */
    public function destroy_本人以外は削除できない(): void
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        //  Act
        $response = $this->actingAs($user)->delete(route('books.destroy', $book));
        // Assert
        $response->assertForbidden();
    }
}
