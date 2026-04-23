<?php

namespace App\Enums;

enum SignedBy: string
{
    case SELF = 'self';
    case PARENT = 'parent';
    case GUARDIAN = 'guardian';

    /**
     * Get the label for the signed by value
     */
    public function label(): string
    {
        return match ($this) {
            self::SELF => 'Self',
            self::PARENT => 'Parent',
            self::GUARDIAN => 'Guardian',
        };
    }
}
