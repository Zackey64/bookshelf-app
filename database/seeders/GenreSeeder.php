<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 指定された１０件を定義
        $genresData = [
            '小説',
            'ビジネス',
            '技術書',
            '自己啓発',
            'エッセイ',
            '歴史',
            '科学',
            '芸術',
            '料理',
            '旅行',
        ];

        // ループ処理でテーブルに投入
        foreach ($genresData as $genreData) {
            Genre::firstOrCreate([
                'name' => $genreData,
            ]);
        }

    }
}
