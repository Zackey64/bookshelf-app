<?php

namespace App\Enums;

enum ReadingPlanStatus: string
{
    case InProgress = 'in_progress';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::InProgress => '読書中',
            self::Completed => '読了',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::InProgress => 'bg-blue-100 text-blue-800',
            self::Completed => 'bg-green-100 text-green-800',
        };
    }
}
