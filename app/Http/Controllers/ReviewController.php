<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * レビューコントローラー
 */
class ReviewController extends Controller
{
    /**
     * レビューを登録する処理
     */
    public function store(StoreReviewRequest $request, Book $book): RedirectResponse
    {
        $validated = $request->validated();
        $book->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return redirect()->route('books.show', $book)->with('success', 'レビューを投稿しました。');
    }

    /**
     * レビューの編集画面を表示
     */
    public function edit(Review $review): View
    {
        // 認可
        $this->authorize('update', $review);

        return view('reviews.edit', compact('review'));
    }

    /**
     * レビューを更新する処理
     */
    public function update(UpdateReviewRequest $request, Review $review): RedirectResponse
    {
        // 認可
        $this->authorize('update', $review);
        $review->update($request->validated());

        return redirect()->route('books.show', $review->book)->with('success', 'レビューを更新しました。');
    }

    /**
     * レビューを削除する処理
     */
    public function destroy(Review $review): RedirectResponse
    {
        // 認可
        $this->authorize('delete', $review);
        $review->delete();

        return redirect()->route('books.show', $review->book)->with('success', 'レビューを削除しました。');
    }

    /**
     * レビューにいいねを登録・解除する処理
     */
    public function like(Review $review): RedirectResponse
    {
        // このユーザーのいいね一覧
        $likedReviews = auth()->user()->likedReviews();
        // いいねされている→解除
        if ($likedReviews->where('reviews.id', $review->id)->exists()) {
            $likedReviews->detach($review->id);

            return redirect()->route('books.show', $review->book)->with('success', 'いいねを取り消しました。');
        }
        // いいねされていない→登録
        $likedReviews->attach($review->id);

        return redirect()->route('books.show', $review->book)->with('success', 'レビューにいいねしました。');
    }
}
