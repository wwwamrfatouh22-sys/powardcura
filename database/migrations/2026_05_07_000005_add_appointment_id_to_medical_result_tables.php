<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['radiology_results', 'lab_tests'] as $tableName) {
            if (!Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'appointment_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table): void {
                $table->foreignId('appointment_id')
                    ->nullable()
                    ->after('patient_id')
                    ->constrained('appointments')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (['radiology_results', 'lab_tests'] as $tableName) {
            if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'appointment_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropConstrainedForeignId('appointment_id');
            });
        }
    }
};
