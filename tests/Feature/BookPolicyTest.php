<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function update_本人のみ更新できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        // Assert
        $this->assertTrue($user->can('update', $book));
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
        // Assert
        $this->assertTrue($user->can('delete', $book));
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
        // Assert
        $this->assertFalse($user->can('delete', $book));
    }
}
