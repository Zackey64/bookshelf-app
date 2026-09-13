<?php

namespace Tests\Feature;

use App\Enums\ReadingPlanStatus;
use App\Models\ReadingPlan;
use App\Models\User;
use App\Notifications\ReadingPlanReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Psy\Command\Command;
use Tests\TestCase;

class ProcessReadingPlansTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 期日3日前の読書計画にリマインダー通知を送信(): void
    {
        // Arrange
        $user = User::factory()->create();
        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'target_date' => today()->addDays(3),
        ]);
        Notification::fake();
        // Act
        $this->artisan('app:process-reading-plans')->assertExitCode(Command::SUCCESS);
        // Assert
        Notification::assertSentTo(
            $user,
            ReadingPlanReminderNotification::class,
            function ($notification) {
                return $notification->timing === 'three_days_before';
            }
        );
    }

    /** @test */
    public function 期日当日の読書計画にリマインダー通知を送信(): void
    {
        // Arrange
        $user = User::factory()->create();
        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'target_date' => today(),
        ]);
        Notification::fake();
        // Act
        $this->artisan('app:process-reading-plans')->assertExitCode(Command::SUCCESS);
        // Assert
        Notification::assertSentTo(
            $user,
            ReadingPlanReminderNotification::class,
            function ($notification) {
                return $notification->timing === 'on_due_date';
            }
        );
    }

    /** @test */
    public function 期限切れ3日後の読書計画に再エンゲージメント通知を送信(): void
    {
        // Arrange
        $user = User::factory()->create();
        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'status' => ReadingPlanStatus::Expired,
            'target_date' => today()->subDays(3),
        ]);
        Notification::fake();
        // Act
        $this->artisan('app:process-reading-plans')->assertExitCode(Command::SUCCESS);
        // Assert
        Notification::assertSentTo(
            $user,
            ReadingPlanReminderNotification::class,
            function ($notification) {
                return $notification->timing === 'three_days_after';
            }
        );
    }

    /** @test */
    public function 期限を過ぎた読書計画をexpiredに変更(): void
    {
        // Arrange
        $readingPlan = ReadingPlan::factory()->create([
            'status' => ReadingPlanStatus::InProgress,
            'target_date' => today()->subDay(),
        ]);
        // Act
        $this->artisan('app:process-reading-plans')->assertExitCode(Command::SUCCESS);
        // Assert
        $readingPlan->refresh();
        $this->assertTrue(
            $readingPlan->status === ReadingPlanStatus::Expired,
        );
    }
}
