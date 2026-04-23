<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'can_schedule')) {
                $table->dropColumn('can_schedule');
            }
            if (Schema::hasColumn('users', 'can_receive_payment')) {
                $table->dropColumn('can_receive_payment');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_schedule')->default(true);
            $table->boolean('can_receive_payment')->default(true);
        });
    }
};
