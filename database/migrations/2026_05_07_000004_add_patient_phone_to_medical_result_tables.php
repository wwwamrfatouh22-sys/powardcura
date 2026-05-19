<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['radiology_results', 'lab_tests'] as $tableName) {
            if (!Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'patient_phone')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table): void {
                $table->string('patient_phone')->nullable()->after('patient_id')->index();
            });

            $patientPhones = DB::table('patients')->pluck('phone', 'id');

            DB::table($tableName)
                ->whereNull('patient_phone')
                ->orderBy('id')
                ->get(['id', 'patient_id'])
                ->each(function ($result) use ($patientPhones, $tableName): void {
                    $phone = $patientPhones[$result->patient_id] ?? null;

                    if ($phone) {
                        DB::table($tableName)
                            ->where('id', $result->id)
                            ->update(['patient_phone' => $phone]);
                    }
                });
        }
    }

    public function down(): void
    {
        foreach (['radiology_results', 'lab_tests'] as $tableName) {
            if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'patient_phone')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table): void {
                if ($this->indexExists($tableName, $tableName . '_patient_phone_index')) {
                    $table->dropIndex($tableName . '_patient_phone_index');
                }

                $table->dropColumn('patient_phone');
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        return collect(Schema::getIndexes($table))
            ->contains(fn (array $index): bool => ($index['name'] ?? null) === $indexName);
    }
};
