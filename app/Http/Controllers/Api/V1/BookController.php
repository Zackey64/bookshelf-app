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
        $books = Book::with('genres')->paginate(10);

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
