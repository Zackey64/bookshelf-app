<?php

namespace App\Models;

use App\Enums\ReadingPlanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'target_date',
        'completed_at',
        'status',
    ];

    // キャスト
    protected $casts = [
        'target_date' => 'date',
        'completed_at' => 'datetime',
        'status' => ReadingPlanStatus::class, // 列挙型enum
    ];

    // 1つのReadingPlanは1人のUserに所属する（1対多の親）
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // 1つのReadingPlanは1冊のBookを対象にする（1対多の親）
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
