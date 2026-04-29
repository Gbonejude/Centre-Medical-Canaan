<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Schema::table('appointments', function (Blueprint $table) {
    if (! Schema::hasColumn('appointments', 'reference')) {
        $table->string('reference', 7)->unique()->nullable()->after('uuid');
        echo "Column 'reference' added successfully.\n";
    } else {
        echo "Column 'reference' already exists.\n";
    }
});
