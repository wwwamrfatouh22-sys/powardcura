<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'clinic_name')) {
                $table->string('clinic_name')->nullable()->after('type');
            }

            if (!Schema::hasColumn('appointments', 'clinic_address')) {
                $table->string('clinic_address')->nullable()->after('clinic_name');
            }

            if (!Schema::hasColumn('appointments', 'clinic_phone')) {
                $table->string('clinic_phone')->nullable()->after('clinic_address');
            }

            if (!Schema::hasColumn('appointments', 'clinic_fee')) {
                $table->decimal('clinic_fee', 10, 2)->nullable()->after('clinic_phone');
            }

            if (!Schema::hasColumn('appointments', 'clinic_notes')) {
                $table->text('clinic_notes')->nullable()->after('clinic_fee');
            }
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            foreach (['clinic_notes', 'clinic_fee', 'clinic_phone', 'clinic_address', 'clinic_name'] as $column) {
                if (Schema::hasColumn('appointments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
