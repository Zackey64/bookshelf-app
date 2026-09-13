<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use App\Policies\ReviewPolicy;
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
        $policy = new ReviewPolicy;
        // Assert
        $this->assertTrue($policy->update($user, $review));
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
        $policy = new ReviewPolicy;
        // Assert
        $this->assertFalse($policy->update($user, $review));
    }

    /** @test */
    public function destroy_本人のみ削除できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
        ]);
        $policy = new ReviewPolicy;
        // Assert
        $this->assertTrue($policy->delete($user, $review));
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
        $policy = new ReviewPolicy;
        // Assert
        $this->assertFalse($policy->delete($user, $review));
    }
}
