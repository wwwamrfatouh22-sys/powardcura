<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('patients', 'gender')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->enum('gender', ['male', 'female'])->nullable();
            });

            return;
        }

        DB::table('patients')
            ->whereNotNull('gender')
            ->update([
                'gender' => DB::raw("CASE LOWER(gender) WHEN 'male' THEN 'male' WHEN 'female' THEN 'female' ELSE NULL END"),
            ]);

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE patients MODIFY gender ENUM('male', 'female') NULL");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('patients', 'gender') && Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE patients MODIFY gender VARCHAR(255) NULL');
        }
    }
};
