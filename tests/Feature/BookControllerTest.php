<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_書籍一覧を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        Book::factory()->count(3)->create();
        // Act
        $response = $this->actingAs($user)->get(route('books.index'));
        // Assert
        $response->assertOk()->assertViewIs('books.index')->assertViewHas('books');
    }

    /** @test */
    public function index_書籍が0件の場合も表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('books.index'));
        // Assert
        $response->assertOk()->assertViewIs('books.index')->assertViewHas('books');
    }

    /** @test */
    public function index_キーワード検索できる(): void
    {
        // Arrange
        $matchingBook = Book::factory()->create([
            'title' => '該当書籍',
            'author' => '該当著者',
        ]);
        Book::factory()->create([
            'title' => '異なる書籍',
            'author' => '異なる著者',
        ]);
        // Act
        $response = $this->get(route('books.index', ['keyword' => '該当']));
        // Assert
        $response->assertOk()->assertViewHas('books',
            function ($books) use ($matchingBook) {
                return $books->count() === 1
                    && $books->first()->id === $matchingBook->id;
            }
        );
    }

    /** @test */
    public function index_ジャンル検索できる(): void
    {
        // Arrange
        $genre = Genre::factory()->create();
        $matchingBook = Book::factory()->create([
            'title' => '該当書籍',
            'author' => '該当著者',
        ]);
        $matchingBook->genres()->attach($genre);
        Book::factory()->create([
            'title' => '異なる書籍',
            'author' => '異なる著者',
        ]);
        // Act
        $response = $this->get(route('books.index', ['genre' => $genre->id]));
        // Assert
        $response->assertOk()->assertViewHas('books',
            function ($books) use ($matchingBook) {
                return $books->count() === 1
                    && $books->first()->id === $matchingBook->id;
            }
        );
    }

    /** @test */
    public function index_並び順を古い順に変更できる(): void
    {
        // Arrange
        $oldBook = Book::factory()->create([
            'title' => '古い書籍',
            'created_at' => now()->subDays(2),
        ]);
        $newBook = Book::factory()->create([
            'title' => '新しい書籍',
            'created_at' => now(),
        ]);
        // Act
        $response = $this->get(route('books.index', ['sort' => 'oldest']));
        // Assert
        $response->assertOk()->assertViewHas('books',
            function ($books) use ($oldBook, $newBook) {
                return $books->count() === 2
                    && $books->first()->id === $oldBook->id
                    && $books->last()->id === $newBook->id;
            }
        );
    }

    /** @test */
    public function index_並び順を評価順に変更できる(): void
    {
        // Arrange
        $lowBook = Book::factory()->create([
            'title' => '評価の低い書籍',
        ]);
        Review::factory()->create([
            'book_id' => $lowBook->id,
            'rating' => 1,
        ]);
        $highBook = Book::factory()->create([
            'title' => '評価の高い書籍',
        ]);
        Review::factory()->create([
            'book_id' => $highBook->id,
            'rating' => 5,
        ]);
        // Act
        $response = $this->get(route('books.index', ['sort' => 'rating']));
        // Assert
        $response->assertOk()->assertViewHas('books',
            function ($books) use ($lowBook, $highBook) {
                return $books->count() === 2
                    && $books->first()->id === $highBook->id
                    && $books->last()->id === $lowBook->id;
            }
        );
    }

    /** @test */
    public function index_並び順をタイトル順に変更できる(): void
    {
        // Arrange
        $aBook = Book::factory()->create([
            'title' => 'A書籍',
        ]);
        $bBook = Book::factory()->create([
            'title' => 'B書籍',
        ]);
        // Act
        $response = $this->get(route('books.index', ['sort' => 'title']));
        // Assert
        $response->assertOk()->assertViewHas('books',
            function ($books) use ($aBook, $bBook) {
                return $books->count() === 2
                    && $books->first()->id === $aBook->id
                    && $books->last()->id === $bBook->id;
            }
        );
    }

    /** @test */
    public function create_書籍登録画面を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        Genre::factory()->count(3)->create();
        // Act
        $response = $this->actingAs($user)->get(route('books.create'));
        // Assert
        $response->assertOk()->assertViewIs('books.create')->assertViewHas('genres');
    }

    /** @test */
    public function store_書籍を登録できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $data = [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890123',
            'published_date' => '2026-09-01',
            'genres' => [$genre->id],
        ];
        // Act
        $response = $this->actingAs($user)->post(route('books.store'), $data);
        $book = Book::where('title', 'テスト書籍')->first();
        // Assert
        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseHas('books', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function show_書籍詳細を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('books.show', $book));
        // Assert
        $response->assertOk()->assertViewIs('books.show')->assertViewHas('book', $book);
    }

    /** @test */
    public function show_存在しない書籍の場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('books.show', 99999));
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function edit_書籍編集画面を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        Genre::factory()->count(3)->create();
        // Act
        $response = $this->actingAs($user)->get(route('books.edit', $book));
        // Assert
        $response->assertOk()->assertViewIs('books.edit')->assertViewHas('book', $book)->assertViewHas('genres');
    }

    /** @test */
    public function edit_存在しない書籍の場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->get(route('books.edit', 99999));
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function update_書籍を更新できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
            'title' => '更新前の書籍',
        ]);
        $data = [
            'title' => '更新後の書籍',
            'author' => $book->author,
            'isbn' => $book->isbn,
            'published_date' => '2026-09-01',
            'genres' => [$genre->id],
        ];
        // Act
        $response = $this->actingAs($user)->put(route('books.update', $book), $data);
        // Assert
        $response->assertRedirect(route('books.show', $book));

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => '更新後の書籍',
        ]);
    }

    /** @test */
    public function update_存在しない書籍の場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->put(route('books.update', 99999), []);
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function destroy_書籍を削除できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        // Act
        $response = $this->actingAs($user)->delete(route('books.destroy', $book));
        // Assert
        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }

    /** @test */
    public function destroy_存在しない書籍の場合は404(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->delete(route('books.destroy', 99999));
        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function isbn_isbnから書籍情報を取得できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $isbn = '9781234567890';
        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response([
                'items' => [[
                    'volumeInfo' => [
                        'title' => 'テスト書籍',
                        'authors' => ['テスト著者'],
                        'publishedDate' => '2026-09-01',
                        'description' => 'テストです。',
                        'imageLinks' => ['thumbnail' => 'https://example.com/image.jpg'],
                    ],
                ], ],
            ], 200),
        ]);
        // Act
        $response = $this->actingAs($user)->get(route('books.isbn', $isbn));
        // Assert
        $response->assertOk()->assertJson([
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'published_date' => '2026-09-01',
            'description' => 'テストです。',
            'image_url' => 'https://example.com/image.jpg',
        ]);
    }

    /** @test */
    public function isbn_13桁でない場合はエラー(): void
    {
        // Arrange
        $user = User::factory()->create();
        $isbn = '123456';
        // Act
        $response = $this->actingAs($user)->get(route('books.isbn', $isbn));
        // Assert
        $response->assertStatus(422);
    }

    /** @test */
    public function isbn_通信エラー(): void
    {
        // Arrange
        $user = User::factory()->create();
        $isbn = '9781234567890';
        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response([], 500),
        ]);
        // Act
        $response = $this->actingAs($user)->get(route('books.isbn', $isbn));
        // Assert
        $response->assertStatus(500);
    }

    /** @test */
    public function isbn_書籍が見つからない(): void
    {
        // Arrange
        $user = User::factory()->create();
        $isbn = '9781234567890';
        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response(['totalItems' => 0], 200),
        ]);
        // Act
        $response = $this->actingAs($user)->get(route('books.isbn', $isbn));
        // Assert
        $response->assertStatus(404);
    }
}
