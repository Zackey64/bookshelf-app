<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_ランキング一覧を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $books = Book::factory()->count(3)->create();
        foreach ($books as $book) {
            Review::factory()->create([
                'book_id' => $book->id,
                'rating' => 5,
            ]);
        }
        // Act
        $response = $this->actingAs($user)->get(route('ranking.index'));
        // Assert
        $response->assertOk()->assertViewIs('ranking.index')->assertViewHas('rankedBooks');
    }

    /** @test */
    public function index_レビューがある書籍がない場合も表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('ranking.index'));
        // Assert
        $response->assertOk()->assertViewIs('ranking.index')->assertViewHas('rankedBooks');
    }
}
