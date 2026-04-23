<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case VALID = 'valid';
    case EXPIRING_SOON = 'expiring_soon';
    case EXPIRED = 'expired';

    /**
     * Get the translated label.
     */
    public function label(): string
    {
        return match ($this) {
            self::VALID => 'Valid',
            self::EXPIRING_SOON => 'Expiring Soon',
            self::EXPIRED => 'Expired',
        };
    }

    /**
     * Get the color for UI display.
     */
    public function color(): string
    {
        return match ($this) {
            self::VALID => 'green',
            self::EXPIRING_SOON => 'orange',
            self::EXPIRED => 'red',
        };
    }

    /**
     * Get the icon name.
     */
    public function icon(): string
    {
        return match ($this) {
            self::VALID => 'check-circle',
            self::EXPIRING_SOON => 'clock',
            self::EXPIRED => 'x-circle',
        };
    }

    /**
     * Get the badge variant for UI frameworks.
     */
    public function badge(): string
    {
        return match ($this) {
            self::VALID => 'success',
            self::EXPIRING_SOON => 'warning',
            self::EXPIRED => 'danger',
        };
    }
}
