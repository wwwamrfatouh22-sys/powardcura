<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('jobs_training')) {
            return;
        }

        Schema::table('jobs_training', function (Blueprint $table) {
            if (!Schema::hasColumn('jobs_training', 'department')) {
                $table->string('department')->nullable()->after('requirements');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('jobs_training')) {
            return;
        }

        Schema::table('jobs_training', function (Blueprint $table) {
            if (Schema::hasColumn('jobs_training', 'department')) {
                $table->dropColumn('department');
            }
        });
    }
};
