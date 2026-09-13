<?php

namespace App\Http\Controllers;

use App\Enums\ReadingPlanStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    //
    public function index(): View
    {
        $user = auth()->user();

        // 総レビュー数・平均評価
        $reviewStats = $user->reviews()
            ->selectRaw('COUNT(*) as total_reviews, AVG(rating) as average_rating')->first();

        // 読了冊数
        $booksRead = auth()->user()->readingPlans()
            ->where('status', ReadingPlanStatus::Completed)->distinct('book_id')->count('book_id');

        // 評価分布
        $ratingDistribution = collect(range(1, 5))
            ->mapWithKeys(function (int $rating) use ($user) {
                return [$rating => $user->reviews()->where('rating', $rating)->count()];
            });

        // 高評価書籍
        $topRatedBooks = $user->reviews()->where('rating', '>=', 4)
            ->with('book')->orderByDesc('rating')->get()->unique('book_id')->take(5)
            ->map(function ($review) {
                return ['id' => $review->book->id, 'title' => $review->book->title, 'author' => $review->book->author, 'rating' => $review->rating];
            })->values();

        // ジャンル別評価傾向
        $genreRatings = DB::table('reviews')->join('books', 'reviews.book_id', '=', 'books.id')
            ->join('book_genre', 'books.id', '=', 'book_genre.book_id')
            ->join('genres', 'book_genre.genre_id', '=', 'genres.id')
            ->where('reviews.user_id', $user->id)
            ->select('genres.id', 'genres.name', DB::raw('COUNT(reviews.id) as count'), DB::raw('AVG(reviews.rating) as average_rating'))
            ->groupBy('genres.id', 'genres.name')->orderByDesc('average_rating')->orderByDesc('count')->limit(5)->get()
            ->map(function ($genre) {
                return [
                    'id' => $genre->id,
                    'name' => $genre->name,
                    'count' => $genre->count,
                    'average_rating' => $genre->average_rating,
                ];
            })->values();

        $stats = [
            'summary' => [
                'total_reviews' => $reviewStats->total_reviews ?? 0,
                'books_read' => $booksRead,
                'average_rating' => $reviewStats->average_rating ?? 0,
            ],
            'rating_distribution' => $ratingDistribution,
            'top_rated_books' => $topRatedBooks,
            'genre_ratings' => $genreRatings,
        ];

        return view('reports.index', compact('stats'));
    }
}
