<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function update_本人は更新できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
        ]);
        // Assert
        $this->assertTrue($user->can('update', $review));
    }

    /** @test */
    public function update_本人以外は更新できない(): void
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        // Assert
        $this->assertFalse($user->can('update', $review));
    }

    /** @test */
    public function destroy_本人のみ削除できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
        ]);
        // Assert
        $this->assertTrue($user->can('delete', $review));
    }

    /** @test */
    public function destroy_本人以外は削除できない(): void
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        // Assert
        $this->assertFalse($user->can('delete', $review));
    }
}
