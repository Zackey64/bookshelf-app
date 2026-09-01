<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Book;
use App\Models\Review;

class ReviewController extends Controller
{
    // レビュー投稿
    public function store(StoreReviewRequest $request, Book $book)
    {
        $validated = $request->validated();
        $book->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'レビューを投稿しました。');
    }

    // レビュー編集画面
    public function edit(Review $review)
    {
        // 認可
        $this->authorize('update', $review);

        return view('reviews.edit', compact('review'));
    }

    // レビュー編集処理
    public function update(UpdateReviewRequest $request, Review $review)
    {
        // 認可
        $this->authorize('update', $review);
        $review->update($request->validated());

        return redirect()->route('books.show', $review->book)->with('success', 'レビューを更新しました。');
    }

    //
    public function destroy(Review $review)
    {
        // 認可
        $this->authorize('delete', $review);
        $review->delete();

        return redirect()->route('books.show', $review->book)->with('success', 'レビューを削除しました。');
    }

    /**
     * レビューいいね
     */
    public function like(Review $review)
    {
        // このユーザーのいいね一覧
        $likedReviews = auth()->user()->likedReviews();
        // いいねされている→解除
        if ($likedReviews->where('reviews.id', $review->id)->exists()) {
            $likedReviews->detach($review->id);

            return back()->with('success', 'いいねを取り消しました。');
        }
        // いいねされていない→登録
        $likedReviews->attach($review->id);

        return back()->with('success', 'レビューにいいねしました。');
    }
}
