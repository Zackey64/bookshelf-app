<?php

namespace Tests\Feature;

use App\Models\ReadingPlan;
use App\Models\User;
use App\Policies\ReadingPlanPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function update_本人のみ読書計画を更新できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);
        $policy = new ReadingPlanPolicy;
        // Assert
        $this->assertTrue($policy->update($user, $readingPlan));
    }

    /** @test */
    public function update_本人以外は更新できない(): void
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $policy = new ReadingPlanPolicy;
        // Assert
        $this->assertFalse($policy->update($user, $readingPlan));
    }

    /** @test */
    public function destroy_本人のみ削除できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);
        $policy = new ReadingPlanPolicy;
        // Assert
        $this->assertTrue($policy->delete($user, $readingPlan));
    }

    /** @test */
    public function destroy_本人以外は削除できない(): void
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $policy = new ReadingPlanPolicy;
        // Assert
        $this->assertFalse($policy->delete($user, $readingPlan));
    }

    /** @test */
    public function complete_本人のみ読了できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);
        $policy = new ReadingPlanPolicy;
        // Assert
        $this->assertTrue($policy->complete($user, $readingPlan));
    }

    /** @test */
    public function complete_本人以外は読了できない(): void
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $policy = new ReadingPlanPolicy;
        // Assert
        $this->assertFalse($policy->complete($user, $readingPlan));
    }
}
