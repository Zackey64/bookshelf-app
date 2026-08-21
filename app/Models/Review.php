<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'user_id',
        'rating',
        'comment',
    ];

    // レビューが投稿された書籍（多対1）
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    // レビューを投稿したユーザー（多対1）
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // レビューに対していいねしたユーザー一覧（多対多）

    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'review_likes')->withTimestamps();
    }
}
