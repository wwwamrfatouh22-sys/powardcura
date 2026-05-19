<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (! Schema::hasColumn('appointments', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('reason');
            }

            if (! Schema::hasColumn('appointments', 'canceled_at')) {
                $table->timestamp('canceled_at')->nullable()->after('status');
            }
        });

        if ($this->indexExists('appointments', 'appointments_doctor_date_time_type_unique')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropUnique('appointments_doctor_date_time_type_unique');
            });
        }
    }

    public function down(): void
    {
        if (! $this->hasDuplicateSlots() && ! $this->indexExists('appointments', 'appointments_doctor_date_time_type_unique')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->unique(['doctor_id', 'date', 'time', 'type'], 'appointments_doctor_date_time_type_unique');
            });
        }

        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'canceled_at')) {
                $table->dropColumn('canceled_at');
            }

            if (Schema::hasColumn('appointments', 'cancellation_reason')) {
                $table->dropColumn('cancellation_reason');
            }
        });
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
            ->where(function ($query): void {
                $query->whereNull('status')
                    ->orWhereNotIn('status', ['Canceled', 'Cancelled', 'canceled', 'cancelled']);
            })
            ->groupBy('doctor_id', 'date', 'time', 'type')
            ->havingRaw('COUNT(*) > 1')
            ->limit(1)
            ->exists();
    }
};
