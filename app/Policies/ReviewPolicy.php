<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * 新規登録は誰でも可能
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * 編集できるのは本人だけ
     */
    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }

    /**
     * 削除ができるのは本人だけ
     */
    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }
}
