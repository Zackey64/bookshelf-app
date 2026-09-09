<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReadingPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $yamada = User::find(1);
        $suzuki = User::find(2);

        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => Book::find(1)->id,
            'target_date' => Carbon::today()->addDays(3),
            'status' => 'in_progress',
        ]);
        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => Book::find(2)->id,
            'target_date' => Carbon::today(),
            'status' => 'in_progress',
        ]);
        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => Book::find(3)->id,
            'target_date' => Carbon::today()->subDays(3),
            'status' => 'in_progress',
        ]);
        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => Book::find(4)->id,
            'target_date' => Carbon::today()->addDays(7),
            'status' => 'in_progress',
        ]);
        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => Book::find(5)->id,
            'target_date' => Carbon::today()->subDays(10),
            'status' => 'completed',
            'completed_at' => Carbon::today()->subDays(5),
        ]);

        ReadingPlan::create([
            'user_id' => $suzuki->id,
            'book_id' => Book::find(6)->id,
            'target_date' => Carbon::today()->addDays(5),
            'status' => 'in_progress',
        ]);

    }
}
