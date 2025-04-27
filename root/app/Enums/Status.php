<?php

namespace App\Enums;

enum Status: string
{
    case todo = 'To do';
    case in_progress = 'In progress';
    case done = 'Done';
    case deadline = 'Deadline';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
