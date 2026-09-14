<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

/**
 * 書籍コントローラー
 */
class BookController extends Controller
{
    /**
     * 書籍の一覧を表示
     */
    public function index(): View
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

    /**
     * 書籍の作成画面を表示
     */
    public function create(): View
    {
        $genres = Genre::all();

        return view('books.create', compact('genres'));
    }

    /**
     * 書籍を登録する処理
     */
    public function store(StoreBookRequest $request): RedirectResponse
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

    /**
     * 書籍の詳細画面を表示
     */
    public function show(Book $book): View
    {
        $book->load([
            'genres',
            'reviews.user',
        ]);

        return view('books.show', compact('book'));
    }

    /**
     * 書籍の編集画面を表示
     */
    public function edit(Book $book): View
    {
        // 認可
        $this->authorize('update', $book);
        $genres = Genre::all();

        return view('books.edit', compact('book', 'genres'));
    }

    /**
     * 書籍を更新する処理
     */
    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
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

    /**
     * 書籍を削除する処理
     */
    public function destroy(Book $book): RedirectResponse
    {
        // 認可
        $this->authorize('delete', $book);
        $book->delete();

        return redirect()->route('books.index')->with('success', '書籍を削除しました。');
    }

    /**
     * 書籍をAPIでISBN検索する処理
     */
    public function isbn(string $isbn): JsonResponse
    {
        // ISBNが13桁の数字かチェック
        if (! preg_match('/^[0-9]{13}$/', $isbn)) {
            return response()->json(['error' => 'ISBNは13桁で入力してください。'], 422);
        }

        // Google Books APIへリクエスト
        $response = Http::get(
            'https://www.googleapis.com/books/v1/volumes',
            [
                'q' => 'isbn:'.$isbn,
                'maxResults' => 1,
                'key' => config('services.google_books.api_key'),
            ]
        );

        // API通信エラー
        if (! $response->successful()) {
            return response()->json(['error' => 'Google Books APIへの接続に失敗しました。'], 500);
        }

        $data = $response->json();

        // 書籍が見つからない
        if (empty($data['items'])) {
            return response()->json(['error' => '該当する書籍が見つかりませんでした。'], 404);
        }

        // 書籍情報を取得
        $volumeInfo = $data['items'][0]['volumeInfo'] ?? [];

        // JSONを返す
        return response()->json([
            'title' => $volumeInfo['title'] ?? '',
            'author' => $volumeInfo['authors'][0] ?? '',
            'published_date' => $volumeInfo['publishedDate'] ?? null,
            'description' => $volumeInfo['description'] ?? '',
            'image_url' => $volumeInfo['imageLinks']['thumbnail'] ?? '',
        ]);
    }
}
