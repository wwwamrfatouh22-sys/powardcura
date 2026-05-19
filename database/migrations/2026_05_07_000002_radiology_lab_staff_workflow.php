<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('staff') && Schema::hasColumn('staff', 'role')) {
            DB::table('staff')
                ->whereIn('role', ['radiology', 'laboratory'])
                ->update(['role' => 'radiology_lab']);
        }

        if (Schema::hasTable('patients') && Schema::hasColumn('patients', 'file_number')) {
            DB::table('patients')
                ->whereNull('file_number')
                ->orWhere('file_number', '')
                ->orderBy('id')
                ->get(['id'])
                ->each(function ($patient): void {
                    DB::table('patients')
                        ->where('id', $patient->id)
                        ->update(['file_number' => 'PAT' . str_pad((string) $patient->id, 6, '0', STR_PAD_LEFT)]);
                });

            Schema::table('patients', function (Blueprint $table): void {
                if (!$this->indexExists('patients', 'patients_file_number_index')) {
                    $table->index('file_number', 'patients_file_number_index');
                }
            });
        }

        foreach (['lab_tests', 'radiology_results'] as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (!Schema::hasColumn($tableName, 'uploaded_by_staff_id')) {
                    $table->foreignId('uploaded_by_staff_id')
                        ->nullable()
                        ->after('patient_id')
                        ->constrained('staff')
                        ->nullOnDelete();
                }

                if (!Schema::hasColumn($tableName, 'notes')) {
                    $table->text('notes')->nullable()->after('description');
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['lab_tests', 'radiology_results'] as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (Schema::hasColumn($tableName, 'uploaded_by_staff_id')) {
                    $table->dropConstrainedForeignId('uploaded_by_staff_id');
                }

                if (Schema::hasColumn($tableName, 'notes')) {
                    $table->dropColumn('notes');
                }
            });
        }

        if (Schema::hasTable('patients') && $this->indexExists('patients', 'patients_file_number_index')) {
            Schema::table('patients', function (Blueprint $table): void {
                $table->dropIndex('patients_file_number_index');
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        return collect(Schema::getIndexes($table))
            ->contains(fn (array $index): bool => ($index['name'] ?? null) === $indexName);
    }
};
