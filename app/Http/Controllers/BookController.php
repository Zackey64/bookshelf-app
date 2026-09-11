<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    // 書籍一覧画面
    public function index()
    {

        $query = Book::query()->with('genres')->withAvg('reviews', 'rating');

        // キーワード検索
        if (request('keyword')) {
            $keyword = request('keyword');
            $query->where(
                function ($query) use ($keyword) {
                    $query->where('title', 'like', "%{$keyword}%")->orWhere('author', 'like', "%{$keyword}%");
                }
            );
        }

        // ジャンル検索
        if (request('genre')) {
            $query->whereHas('genres',
                function ($query) {
                    $query->where('genres.id', request('genre'));
                }
            );
        }

        // 並び順
        switch (request('sort', 'newest')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'rating':
                $query->orderByDesc('reviews_avg_rating');
                break;
            case 'title':
                $query->orderBy('title');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $books = $query->paginate(10)->withQueryString();
        $genres = Genre::orderBy('name')->get();

        return view('books.index', compact('books', 'genres'));
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
            'published_date' => $validated['published_date'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);
        $book->genres()->attach($request->validated('genres'));

        return redirect()->route('books.show', $book)->with('success', '書籍を登録しました。');
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
            'published_date' => $validated['published_date'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);
        $book->genres()->sync($validated['genres']);

        return redirect()->route('books.show', $book)->with('success', '書籍を更新しました。');
    }

    // 書籍削除処理
    public function destroy(Book $book)
    {
        // 認可
        $this->authorize('delete', $book);
        $book->delete();

        return redirect()->route('books.index')->with('success', '書籍を削除しました。');
    }

    // ISBN検索
    public function isbn(string $isbn): JsonResponse
    {

        // あとで

        return response()->json([]);
    }
}
