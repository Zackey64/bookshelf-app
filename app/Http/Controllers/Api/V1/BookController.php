<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreBookRequest;
use App\Http\Requests\Api\V1\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;

class BookController extends Controller
{
    // 一覧
    public function index()
    {
        $query = Book::query()->with('genres')->withAvg('reviews', 'rating')->withCount('reviews');
        // キーワード検索
        if (request('keyword')) {
            $keyword = request('keyword');
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('author', 'like', "%{$keyword}%");
            });
        }
        // ジャンル絞り込み
        if (request('genre')) {
            $query->whereHas('genres', function ($query) {
                $query->where('genres.id', request('genre'));
            });
        }

        $books = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return BookResource::collection($books);
    }

    // 詳細
    public function show(Book $book)
    {
        $book->load('genres');

        return new BookResource($book);
    }

    // 作成
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

    // 編集
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

    // 削除
    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);
        $book->delete();

        return response()->json(null, 204);
    }
}
