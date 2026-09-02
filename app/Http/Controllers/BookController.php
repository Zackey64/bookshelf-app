<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;

class BookController extends Controller
{
    // 書籍一覧画面
    public function index()
    {
        $books = Book::with('genres')->latest()->paginate(10);

        return view('books.index', compact('books'));
    }

    // 書籍登録画面
    public function create()
    {
        $genres = Genre::all();

        return view('books.create', compact('genres'));
    }

    // 書籍登録処理
    public function store(StoreBookRequest $request)
    {
        $validated = $request->validated();

        $book = auth()->user()->books()->create([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'isbn' => $validated['isbn'],
            'published_date' => $validated['published_date'],
            'image_url' => $validated['image_url'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);
        $book->genres()->attach($request->validated('genres'));

        return redirect()->route('books.index', $book)->with('success', '書籍を登録しました。');
    }

    // 書籍詳細画面
    public function show(Book $book)
    {
        $book->load([
            'genres',
            'reviews.user',
        ]);

        return view('books.show', compact('book'));
    }

    // 書籍編集画面
    public function edit(Book $book)
    {
        // 認可
        $this->authorize('update', $book);

        $genres = Genre::all();

        return view('books.edit', compact('book', 'genres'));
    }

    // 書籍編集処理
    public function update(UpdateBookRequest $request, Book $book)
    {
        // 認可
        $this->authorize('update', $book);

        $validated = $request->validated();
        $book->update([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'isbn' => $validated['isbn'],
            'published_date' => $validated['published_date'],
            'image_url' => $validated['image_url'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);
        $book->genres()->sync($validated['genres']);

        return redirect()->route('books.index', $book)->with('success', '書籍を更新しました。');
    }

    // 書籍削除処理
    public function destroy(Book $book)
    {
        // 認可
        $this->authorize('delete', $book);
        $book->delete();

        return redirect()->route('books.index')->with('success', '書籍を削除しました。');
    }
}
