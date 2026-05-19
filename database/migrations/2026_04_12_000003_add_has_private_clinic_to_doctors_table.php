<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            if (!Schema::hasColumn('doctors', 'has_private_clinic')) {
                $table->boolean('has_private_clinic')->default(true)->after('status');
            }
        });

        DB::table('doctors')
            ->whereNull('has_private_clinic')
            ->update(['has_private_clinic' => true]);
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            if (Schema::hasColumn('doctors', 'has_private_clinic')) {
                $table->dropColumn('has_private_clinic');
            }
        });
    }
};
