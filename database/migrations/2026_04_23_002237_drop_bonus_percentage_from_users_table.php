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
            if (Schema::hasColumn('users', 'bonus_percentage')) {
                $table->dropColumn('bonus_percentage');
            }
            // On en profite pour enlever aussi les autres colonnes de paie/canaan restées
            $otherColumns = ['can_schedule', 'can_receive_payment', 'employment_type', 'hourly_rate'];
            foreach ($otherColumns as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('bonus_percentage', 5, 2)->nullable();
        });
    }
};
