<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_ユーザーと１対多の関係を持つ(): void
    {
        // Arrange
        $user = User::factory()->create();
        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);
        // Assert
        $this->assertTrue($readingPlan->user->is($user));
    }

    /** @test */
    public function book_書籍と１対多の関係を持つ(): void
    {
        // Arrange
        $book = Book::factory()->create();
        $readingPlan = ReadingPlan::factory()->create([
            'book_id' => $book->id,
        ]);
        // Assert
        $this->assertTrue($readingPlan->book->is($book));
    }
}
