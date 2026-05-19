<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('jobs_training')) {
            Schema::table('jobs_training', function (Blueprint $table) {
                if (!Schema::hasColumn('jobs_training', 'status')) {
                    $table->string('status')->default('active')->after('type');
                }
            });

            DB::table('jobs_training')
                ->where('type', 'admin')
                ->update(['type' => 'administrative']);

            DB::table('jobs_training')
                ->whereNull('type')
                ->update(['type' => 'medical']);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('jobs_training')) {
            DB::table('jobs_training')
                ->where('type', 'administrative')
                ->update(['type' => 'admin']);

            Schema::table('jobs_training', function (Blueprint $table) {
                if (Schema::hasColumn('jobs_training', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }
};
