<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use App\Notifications\ReadingPlanReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanReminderNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function データベース通知を使用(): void
    {
        // Arrange
        $notification = new ReadingPlanReminderNotification(
            ReadingPlan::factory()->create(),
            'test',
            'テスト通知',
            'テスト通知です。'
        );
        $user = User::factory()->create();
        // Act
        $channels = $notification->via($user);
        // Assert
        $this->assertSame(['database'], $channels);
    }

    /** @test */
    public function 通知データを正しく返す(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
        $notification = new ReadingPlanReminderNotification(
            $readingPlan,
            'test',
            'テスト通知',
            'テスト通知です。'
        );
        // Act
        $data = $notification->toArray($user);
        // Assert
        $this->assertSame([
            'reading_plan_id' => $readingPlan->id,
            'timing' => 'test',
            'title' => 'テスト通知',
            'body' => 'テスト通知です。',
        ], $data);
    }
}
