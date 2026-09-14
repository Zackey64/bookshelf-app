<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * お気に入り書籍コントローラー
 */
class FavoriteController extends Controller
{
    /**
     * お気に入り書籍の一覧を表示
     */
    public function index(): View
    {
        $books = auth()->user()->favoriteBooks()
            ->latest('favorites.created_at')->paginate(10);

        return view('favorites.index', compact('books'));
    }

    /**
     * お気に入り書籍を登録・解除する処理
     */
    public function toggle(Book $book): RedirectResponse
    {
        // このユーザーのお気に入り書籍
        $favoriteBooks = auth()->user()->favoriteBooks();
        // お気に入り一覧に登録されている→解除
        if ($favoriteBooks->where('books.id', $book->id)->exists()) {
            $favoriteBooks->detach($book->id);

            return back()->with('success', 'お気に入りから削除しました。');
        }
        // お気に入りに登録されていない→登録
        $favoriteBooks->attach($book->id);

        return back()->with('success', 'お気に入りに追加しました。');

    }
}
