<?php

namespace Tests\Feature;

use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function store_必須項目がない場合(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->post(route('reading-plans.store'), []);
        // Assert
        $response->assertSessionHasErrors([
            'book_id',
            'target_date',
        ]);
    }

    /** @test */
    public function update_必須項目がない場合(): void
    {
        // Arrange
        $user = User::factory()->create();
        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);

        // Act
        $response = $this->actingAs($user)
            ->put(route('reading-plans.update', $readingPlan), []);

        // Assert
        $response->assertSessionHasErrors([
            'target_date',
        ]);
    }
}
