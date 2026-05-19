<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                if (!Schema::hasColumn('job_applications', 'full_name')) {
                    $table->string('full_name')->nullable()->after('name');
                }

                if (!Schema::hasColumn('job_applications', 'cv_path')) {
                    $table->string('cv_path')->nullable()->after('cv');
                }

                if (!Schema::hasColumn('job_applications', 'status')) {
                    $table->string('status')->default('pending')->after('cv_path');
                }
            });

            DB::table('job_applications')
                ->whereNull('full_name')
                ->update(['full_name' => DB::raw('name')]);

            DB::table('job_applications')
                ->whereNull('cv_path')
                ->update(['cv_path' => DB::raw('cv')]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                if (Schema::hasColumn('job_applications', 'status')) {
                    $table->dropColumn('status');
                }

                if (Schema::hasColumn('job_applications', 'cv_path')) {
                    $table->dropColumn('cv_path');
                }

                if (Schema::hasColumn('job_applications', 'full_name')) {
                    $table->dropColumn('full_name');
                }
            });
        }
    }
};
