<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'published_date',
        'image_url',
        'description',
    ];

    // 書籍に紐づくジャンル一覧（多対多）
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class)->withTimestamps();
    }

    // 書籍に対するレビュー一覧（1対多）
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // 書籍をお気に入り登録しているユーザー一覧（多対多）
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }
}
