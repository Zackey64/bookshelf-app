<?php

namespace App\Console\Commands;

use App\Enums\ReadingPlanStatus;
use App\Models\ReadingPlan;
use App\Notifications\ReadingPlanReminderNotification;
use Illuminate\Console\Command;

class ProcessReadingPlans extends Command
{
    protected $signature = 'app:process-reading-plans';
    protected $description = '読書計画の期限更新とリマインダー通知処理';

    public function handle()
    {
        $today = today();

        // 期日3日前の予告通知
        ReadingPlan::query()
            ->with('book', 'user')->where('status', ReadingPlanStatus::InProgress)
            ->whereDate('target_date', $today->copy()->addDays(3))
            ->get()->each(
                function (ReadingPlan $readingPlan) {
                    $readingPlan->user->notify(
                        new ReadingPlanReminderNotification(
                            $readingPlan,
                            'three_days_before',
                            '読書計画の期日が近づいています',
                            "「{$readingPlan->book->title}」の期日まであと3日です。"
                        )
                    );
                }
            );

        // 期日当日の最終リマインド通知
        ReadingPlan::query()
            ->with('book', 'user')->where('status', ReadingPlanStatus::InProgress)
            ->whereDate('target_date', $today)
            ->get()->each(
                function (ReadingPlan $readingPlan) {
                    $readingPlan->user->notify(
                        new ReadingPlanReminderNotification(
                            $readingPlan,
                            'on_due_date',
                            '読書計画の期日です',
                            "「{$readingPlan->book->title}」の期日は今日です。"
                        )
                    );
                }
            );

        // 期日3日後の expired に再エンゲージメント通知
        ReadingPlan::query()
            ->with('book', 'user')->where('status', ReadingPlanStatus::Expired)
            ->whereDate('target_date', $today->copy()->subDays(3))
            ->get()->each(
                function (ReadingPlan $readingPlan) {
                    $readingPlan->user->notify(
                        new ReadingPlanReminderNotification(
                            $readingPlan,
                            'three_days_after',
                            '読書計画の期日が過ぎています',
                            "「{$readingPlan->book->title}」の読書計画が期限切れになっています。"
                        )
                    );
                }
            );

        // 期限を過ぎた in_progress を expired に変更
        ReadingPlan::query()
            ->where('status', ReadingPlanStatus::InProgress)
            ->whereDate('target_date', '<', $today)
            ->update(['status' => ReadingPlanStatus::Expired]);

        return self::SUCCESS;
    }
}
