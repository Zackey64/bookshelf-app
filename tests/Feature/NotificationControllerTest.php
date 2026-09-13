<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_通知を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $user->notifications()->create([
            'id' => fake()->uuid(),
            'type' => 'App\Notifications\ReadingPlanReminderNotification',
            'data' => [
                'timing' => 'on_due_date',
                'title' => '読書計画の期日です',
                'body' => '「テスト書籍」の期日は今日です。',
            ],
        ]);
        // Act
        $response = $this->actingAs($user)->get(route('notifications.index'));
        // Assert
        $response->assertOk()->assertViewIs('notifications.index')->assertViewHas('notifications');
    }

    /** @test */
    public function index_通知が0件の場合も表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('notifications.index'));
        // Assert
        $response->assertOk()->assertViewIs('notifications.index')->assertViewHas('notifications');
    }

    /** @test */
    public function mark_as_read_通知を既読にできる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $notification = $user->notifications()->create([
            'id' => fake()->uuid(),
            'type' => 'App\Notifications\ReadingPlanReminderNotification',
            'data' => [
                'timing' => 'on_due_date',
                'title' => '読書計画の期日です',
                'body' => '「テスト書籍」の期日は今日です。',
            ],
        ]);
        // Act
        $response = $this->actingAs($user)->post(route('notifications.read', $notification->id));
        // Assert
        $response->assertRedirect(route('notifications.index'));
        $this->assertNotNull($notification->fresh()->read_at);
    }
}
