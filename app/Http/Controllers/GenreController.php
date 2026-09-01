<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Models\Genre;

class GenreController extends Controller
{
    // ジャンル一覧画面
    public function index()
    {
        // 全ジャンルを取得＋紐づいている書籍数を追加取得
        $genres = Genre::withCount('books')->get();

        // 画面表示
        return view('genres.index', compact('genres'));
    }

    // ジャンル登録画面
    public function create()
    {
        // 画面表示
        return view('genres.create');
    }

    // ジャンル登録処理
    public function store(StoreGenreRequest $request)
    {
        // ジャンル作成
        Genre::create($request->validated());

        // リダイレクト
        return redirect()->route('genres.index')->with('success', 'ジャンルを登録しました。');
    }

    // ジャンル詳細画面
    public function show(Genre $genre)
    {
        // ジャンルに紐づいている書籍を取得
        $books = $genre->books()->latest()->paginate(10);

        // 画面表示
        return view('genres.show', compact('genre', 'books'));
    }

    // ジャンル編集画面
    public function edit(Genre $genre)
    {
        // 画面表示
        return view('genres.edit', compact('genre'));
    }

    // ジャンル更新処理
    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        // ジャンル更新
        $genre->update($request->validated());

        // リダイレクト
        return redirect()->route('genres.index')->with('success', 'ジャンルを更新しました。');
    }

    // ジャンル削除処理
    public function destroy(Genre $genre)
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
