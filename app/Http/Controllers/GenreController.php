<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * ジャンルコントローラー
 */
class GenreController extends Controller
{
    /**
     * ジャンルの一覧を表示
     */
    public function index(): View
    {
        // 全ジャンルを取得＋紐づいている書籍数を追加取得
        $genres = Genre::withCount('books')->get();

        // 画面表示
        return view('genres.index', compact('genres'));
    }

    /**
     * ジャンルの作成画面を表示
     */
    public function create(): View
    {
        // 画面表示
        return view('genres.create');
    }

    /**
     * ジャンルを登録する処理
     */
    public function store(StoreGenreRequest $request): RedirectResponse
    {
        // ジャンル作成
        Genre::create($request->validated());

        // リダイレクト
        return redirect()->route('genres.index')->with('success', 'ジャンルを登録しました。');
    }

    /**
     * ジャンルの詳細画面を表示
     */
    public function show(Genre $genre): View
    {
        // ジャンルに紐づいている書籍を取得
        $books = $genre->books()->latest()->paginate(10);

        // 画面表示
        return view('genres.show', compact('genre', 'books'));
    }

    /**
     * ジャンルの編集画面を表示
     */
    public function edit(Genre $genre): View
    {
        // 画面表示
        return view('genres.edit', compact('genre'));
    }

    /**
     * ジャンルを更新する処理
     */
    public function update(UpdateGenreRequest $request, Genre $genre): RedirectResponse
    {
        // ジャンル更新
        $genre->update($request->validated());

        // リダイレクト
        return redirect()->route('genres.index')->with('success', 'ジャンルを更新しました。');
    }

    /**
     * ジャンルを削除する処理
     */
    public function destroy(Genre $genre): RedirectResponse
    {
        // 紐付きがある場合は削除を制限
        if ($genre->books()->exists()) {
            return back()->with('error', '書籍が登録されているジャンルは削除できません。');
        }
        // ジャンル削除
        $genre->delete();

        // リダイレクト
        return redirect()->route('genres.index')->with('success', 'ジャンルを削除しました。');
    }
}
