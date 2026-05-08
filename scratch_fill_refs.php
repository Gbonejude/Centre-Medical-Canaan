<?php

use App\Models\Appointment;

Appointment::whereNull('reference')->get()->each(function ($a) {
    do {
        $reference = str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
    } while (Appointment::where('reference', $reference)->exists());

    $a->update(['reference' => $reference]);
    echo "Updated appointment {$a->id} with reference {$reference}\n";
});
