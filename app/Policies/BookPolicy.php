<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    /**
     * 新規登録は誰でも可能
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * 本の編集・更新ができるのは本人だけ
     */
    public function update(User $user, Book $book): bool
    {
        return $user->id === $book->user_id;
    }

    /**
     * 本の削除ができるのは本人だけ
     */
    public function delete(User $user, Book $book): bool
    {
        return $user->id === $book->user_id;
    }
}
