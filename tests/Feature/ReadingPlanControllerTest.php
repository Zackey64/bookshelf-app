<?php

namespace Tests\Feature;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_読書計画一覧を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        ReadingPlan::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);
        // Act
        $response = $this->actingAs($user)->get(route('reading-plans.index'));
        // Assert
        $response->assertOk()->assertViewIs('reading-plans.index')->assertViewHas('readingPlans');
    }

    /** @test */
    public function index_読書計画が0件の場合も表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('reading-plans.index'));
        // Assert
        $response->assertOk()->assertViewIs('reading-plans.index')->assertViewHas('readingPlans');
    }

    /** @test */
    public function index_検索できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $completedPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'status' => ReadingPlanStatus::Completed,
        ]);
        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'status' => ReadingPlanStatus::InProgress,
        ]);
        // Act
        $response = $this->actingAs($user)->get(route('reading-plans.index', [
            'status' => ReadingPlanStatus::Completed->value,
        ]));
        // Assert
        $response->assertOk()->assertViewIs('reading-plans.index')->assertViewHas('readingPlans',
            function ($readingPlans) use ($completedPlan) {
                return $readingPlans->count() === 1
                    && $readingPlans->first()->id === $completedPlan->id;
            }
        );
    }

    /** @test */
    public function create_読書計画登録画面を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        Book::factory()->count(3)->create();
        // Act
        $response = $this->actingAs($user)->get(route('reading-plans.create'));
        // Assert
        $response->assertOk()->assertViewIs('reading-plans.create')->assertViewHas('books');
    }

    /** @test */
    public function store_読書計画を登録できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $data = [
            'book_id' => $book->id,
            'target_date' => '2026-09-01',
        ];
        // Act
        $response = $this->actingAs($user)->post(route('reading-plans.store'), $data);
        // Assert
        $response->assertRedirect(route('reading-plans.index'));
        $this->assertDatabaseHas('reading_plans', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function edit_読書計画編集画面を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);
        // Act
        $response = $this->actingAs($user)->get(route('reading-plans.edit', $readingPlan));
        // Assert
        $response->assertOk()->assertViewIs('reading-plans.edit')->assertViewHas('readingPlan', $readingPlan);
    }

    /** @test */
    public function edit_存在しない読書計画の場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('reading-plans.edit', 99999));
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function update_読書計画を更新できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);
        $data = [
            'target_date' => '2026-09-01',
        ];
        // Act
        $response = $this->actingAs($user)->put(route('reading-plans.update', $readingPlan), $data);
        // Assert
        $response->assertRedirect(route('reading-plans.index'));
        $this->assertDatabaseHas('reading_plans', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function update_存在しない読書計画の場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->put(route('reading-plans.update', 99999), []);
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function destroy_読書計画を削除できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);
        // Act
        $response = $this->actingAs($user)->delete(route('reading-plans.destroy', $readingPlan));
        // Assert
        $response->assertRedirect(route('reading-plans.index'));
        $this->assertDatabaseMissing('reading_plans', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function destroy_存在しない読書計画の場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->delete(route('reading-plans.destroy', 99999));
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function complete_読書計画を読了できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);
        // Act
        $response = $this->actingAs($user)->post(route('reading-plans.complete', $readingPlan));
        // Assert
        $response->assertRedirect(route('reading-plans.index'));
        $this->assertDatabaseHas('reading_plans', [
            'status' => ReadingPlanStatus::Completed,
        ]);
    }

    /** @test */
    public function complete_存在しない読書計画の場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->post(route('reading-plans.complete', 99999));
        // Assert
        $response->assertNotFound();
    }
}
