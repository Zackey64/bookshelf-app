<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_レポートを表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('reports.index'));
        // Assert
        $response->assertOk()->assertViewIs('reports.index')->assertViewHas('stats');
    }

    /** @test */
    public function index_レポートに集計データを渡せる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();
        $book->genres()->attach($genre);
        Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => 5, ]);
        // Act
        $response = $this->actingAs($user)->get(route('reports.index'));
        // Assert
        $response->assertViewHas('stats', function ($stats) use ($genre) {
            $genreRating = collect($stats['genre_ratings'])->firstWhere('id', $genre->id);

            return isset($stats['summary'])
                && isset($stats['rating_distribution'])
                && isset($stats['top_rated_books'])
                && isset($stats['genre_ratings'])
                && $genreRating !== null
                && $genreRating['name'] === $genre->name
                && $genreRating['count'] === 1
                && $genreRating['average_rating'] == 5;
        });
    }
}
