<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';
    case POSTPONED = 'POSTPONED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::CONFIRMED => 'Confirmé',
            self::COMPLETED => 'Terminé',
            self::CANCELLED => 'Annulé',
            self::POSTPONED => 'Reporté',
        };
    }

    public static function labels(): array
    {
        return array_reduce(self::cases(), function ($carry, $item) {
            $carry[$item->value] = $item->label();

            return $carry;
        }, []);
    }
}
