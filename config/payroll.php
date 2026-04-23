<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payroll Configuration
    |--------------------------------------------------------------------------
    |
    | These values configure the payroll calculation settings for the
    | application, including regular hourly rate, regular hours threshold,
    | and overtime multiplier.
    |
    */

    'regular_rate' => env('PAYROLL_REGULAR_RATE', 14),
    'regular_hours' => env('PAYROLL_REGULAR_HOURS', 80),

    'regular_hours_threshold' => env('PAYROLL_REGULAR_HOURS_THRESHOLD', 80),

    'overtime_multiplier' => env('PAYROLL_OVERTIME_MULTIPLIER', 1.5),

];
