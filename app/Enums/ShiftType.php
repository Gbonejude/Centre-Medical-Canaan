<?php

namespace App\Enums;

enum ShiftType: string
{
    case MORNING = 'morning';
    case AFTERNOON = 'afternoon';
    case EVENING = 'evening';
    case NIGHT = 'night';
    case DAILY = 'daily';
    case DAY = 'day';

}
