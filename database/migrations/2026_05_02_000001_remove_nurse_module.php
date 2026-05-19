<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('staff_leave_requests') && Schema::hasColumn('staff_leave_requests', 'nurse_id')) {
            try {
                Schema::table('staff_leave_requests', function (Blueprint $table) {
                    $table->dropForeign(['nurse_id']);
                });
            } catch (Throwable) {
                // The legacy column was nullable and usually had no foreign key.
            }

            Schema::table('staff_leave_requests', function (Blueprint $table) {
                $table->dropColumn('nurse_id');
            });
        }

        if (Schema::hasTable('staff_leave_requests') && Schema::hasColumn('staff_leave_requests', 'type')) {
            DB::table('staff_leave_requests')
                ->where('type', 'nurse')
                ->pluck('id')
                ->each(function ($id): void {
                    DB::table('staff_leave_requests')->where('id', $id)->delete();
                });

            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE staff_leave_requests MODIFY type ENUM('doctor')");
            }
        }

        Schema::dropIfExists('nurses');
    }

    public function down(): void
    {
        if (!Schema::hasTable('nurses')) {
            Schema::create('nurses', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('staff_leave_requests') && !Schema::hasColumn('staff_leave_requests', 'nurse_id')) {
            Schema::table('staff_leave_requests', function (Blueprint $table) {
                $table->unsignedBigInteger('nurse_id')->nullable()->after('doctor_id');
            });
        }

        if (Schema::hasTable('staff_leave_requests') && Schema::hasColumn('staff_leave_requests', 'type') && DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE staff_leave_requests MODIFY type ENUM('doctor','nurse')");
        }
    }
};
