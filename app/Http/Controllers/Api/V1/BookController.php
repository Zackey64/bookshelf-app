<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexBookRequest;
use App\Http\Requests\Api\V1\StoreBookRequest;
use App\Http\Requests\Api\V1\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 書籍APIコントローラー
 */
class BookController extends Controller
{
    /**
     *  書籍の一覧を取得
     */
    public function index(IndexBookRequest $request): JsonResource
    {
        $query = Book::query()->with('genres')->withAvg('reviews', 'rating')->withCount('reviews');
        // キーワード検索
        if ($request->filled('keyword')) {
            $keyword = $request->validated('keyword');
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('author', 'like', "%{$keyword}%");
            });
        }
        // ジャンル絞り込み
        if ($request->filled('genre_id')) {
            $query->whereHas('genres', function ($query) use ($request) {
                $query->where('genres.id', $request->validated('genre_id'));
            });
        }

        $perPage = $request->validated('per_page', 20);
        $books = $query->paginate($perPage);

        return BookResource::collection($books);
    }

    /**
     *  書籍の詳細を取得
     */
    public function show(Book $book)
    {
        $book->load([
            'genres',
            'reviews.user',
        ]);

        $book->loadAvg('reviews', 'rating');
        $book->loadCount('reviews');

        return new BookResource($book);
    }

    /**
     * 書籍を登録する処理
     */
    public function store(StoreBookRequest $request)
    {
        $validated = $request->validated();

        $genres = $validated['genres'];
        unset($validated['genres']);

        $book = $request->user()->books()->create($validated);

        $book->genres()->sync($genres);
        $book->load('genres');

        return new BookResource($book)->response()->setStatusCode(201);
    }

    /**
     * 書籍を更新する処理
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $this->authorize('update', $book);
        $validated = $request->validated();

        $genres = $validated['genres'];
        unset($validated['genres']);

        $book->update($validated);

        $book->genres()->sync($genres);
        $book->load('genres');

        return new BookResource($book)->response()->setStatusCode(200);
    }

    /**
     * 書籍を削除する処理
     */
    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);
        $book->delete();

        return response()->json(null, 204);
    }
}
