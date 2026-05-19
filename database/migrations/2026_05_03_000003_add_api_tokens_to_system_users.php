<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['admins', 'doctors', 'staff'] as $table) {
            if (!Schema::hasColumn($table, 'api_token')) {
                Schema::table($table, function (Blueprint $table): void {
                    $table->string('api_token', 80)->nullable()->unique();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['admins', 'doctors', 'staff'] as $tableName) {
            if (Schema::hasColumn($tableName, 'api_token')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->dropColumn('api_token');
                });
            }
        }
    }
};
