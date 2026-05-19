<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('radiology_results') && !Schema::hasColumn('radiology_results', 'result_type')) {
            Schema::table('radiology_results', function (Blueprint $table): void {
                $table->string('result_type')->default('radiology')->after('uploaded_by_staff_id');
            });

            DB::table('radiology_results')->update(['result_type' => 'radiology']);
        }

        if (Schema::hasTable('lab_tests') && !Schema::hasColumn('lab_tests', 'result_type')) {
            Schema::table('lab_tests', function (Blueprint $table): void {
                $table->string('result_type')->default('laboratory')->after('uploaded_by_staff_id');
            });

            DB::table('lab_tests')->update(['result_type' => 'laboratory']);
        }
    }

    public function down(): void
    {
        foreach (['radiology_results', 'lab_tests'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'result_type')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->dropColumn('result_type');
                });
            }
        }
    }
};
