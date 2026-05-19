<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('appointments')->whereNull('type')->update(['type' => 'hospital']);

        if ($this->hasDuplicateSlots()) {
            return;
        }

        if (!$this->indexExists('appointments', 'appointments_doctor_date_time_type_unique')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->unique(['doctor_id', 'date', 'time', 'type'], 'appointments_doctor_date_time_type_unique');
            });
        }
    }

    public function down(): void
    {
        if ($this->indexExists('appointments', 'appointments_doctor_date_time_type_unique')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropUnique('appointments_doctor_date_time_type_unique');
            });
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        if (DB::getDriverName() !== 'mysql') {
            return false;
        }

        return collect(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]))->isNotEmpty();
    }

    private function hasDuplicateSlots(): bool
    {
        return DB::table('appointments')
            ->select('doctor_id', 'date', 'time', 'type')
            ->whereNotNull('doctor_id')
            ->whereNotNull('date')
            ->whereNotNull('time')
            ->groupBy('doctor_id', 'date', 'time', 'type')
            ->havingRaw('COUNT(*) > 1')
            ->limit(1)
            ->exists();
    }
};
