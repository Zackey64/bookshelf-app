<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function books_書籍と多対多の関係を持つ(): void
    {
        // Arrange
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();
        $genre->books()->attach($book);
        // Assert
        $this->assertTrue($genre->books->contains($book));
    }
}
