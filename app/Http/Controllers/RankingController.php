<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\View\View;

/**
 * 書籍ランキングコントローラー
 */
class RankingController extends Controller
{
    /**
     * 書籍ランキングの一覧を表示
     */
    public function index(): View
    {
        $rankedBooks = Book::withAvg('reviews', 'rating')
            ->withCount('reviews')->has('reviews')
            ->orderByDesc('reviews_avg_rating')
            ->take(10)->get();

        return view('ranking.index', compact('rankedBooks'));
    }
}
