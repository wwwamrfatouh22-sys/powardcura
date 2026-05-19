<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('complaints')) {
            return;
        }

        Schema::table('complaints', function (Blueprint $table) {
            if (!Schema::hasColumn('complaints', 'subject')) {
                $table->string('subject')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('complaints', 'status')) {
                $table->string('status')->default('pending')->after('details');
            }

            if (!Schema::hasColumn('complaints', 'priority')) {
                $table->string('priority')->default('medium')->after('status');
            }
        });

        DB::table('complaints')
            ->whereNull('subject')
            ->update(['subject' => DB::raw('type')]);

        DB::table('complaints')
            ->whereNull('status')
            ->update(['status' => 'pending']);

        DB::table('complaints')
            ->whereNull('priority')
            ->update(['priority' => 'medium']);
    }

    public function down(): void
    {
        if (!Schema::hasTable('complaints')) {
            return;
        }

        Schema::table('complaints', function (Blueprint $table) {
            if (Schema::hasColumn('complaints', 'priority')) {
                $table->dropColumn('priority');
            }

            if (Schema::hasColumn('complaints', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('complaints', 'subject')) {
                $table->dropColumn('subject');
            }
        });
    }
};
