<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $indexExists = function (string $table, string $index): bool {
            return collect(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]))->isNotEmpty();
        };

        Schema::table('appointments', function (Blueprint $table) {
            //
        });

        if (!$indexExists('appointments', 'appointments_doctor_date_time_index')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->index(['doctor_id', 'date', 'time'], 'appointments_doctor_date_time_index');
            });
        }

        if (!$indexExists('ratings', 'ratings_appointment_id_unique')) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->unique('appointment_id', 'ratings_appointment_id_unique');
            });
        }

        if (!$indexExists('website_ratings', 'website_ratings_appointment_id_unique')) {
            Schema::table('website_ratings', function (Blueprint $table) {
                $table->unique('appointment_id', 'website_ratings_appointment_id_unique');
            });
        }
    }

    public function down(): void
    {
        $indexExists = function (string $table, string $index): bool {
            return collect(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]))->isNotEmpty();
        };

        if ($indexExists('appointments', 'appointments_doctor_date_time_index')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropIndex('appointments_doctor_date_time_index');
            });
        }

        if ($indexExists('ratings', 'ratings_appointment_id_unique')) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->dropUnique('ratings_appointment_id_unique');
            });
        }

        if ($indexExists('website_ratings', 'website_ratings_appointment_id_unique')) {
            Schema::table('website_ratings', function (Blueprint $table) {
                $table->dropUnique('website_ratings_appointment_id_unique');
            });
        }
    }
};
