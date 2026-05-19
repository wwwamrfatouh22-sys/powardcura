<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('staff')) {
            Schema::create('staff', function (Blueprint $table): void {
                $table->id();
                $table->string('name')->nullable();
                $table->string('full_name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('role')->default('receptionist');
                $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
                $table->string('password');
                $table->string('status')->default('active');
                $table->rememberToken();
                $table->string('api_token', 80)->nullable()->unique();
                $table->timestamps();
                $table->softDeletes();
            });

            return;
        }

        Schema::table('staff', function (Blueprint $table): void {
            if (!Schema::hasColumn('staff', 'full_name')) {
                $table->string('full_name')->nullable()->after('name');
            }

            if (!Schema::hasColumn('staff', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            if (!Schema::hasColumn('staff', 'role')) {
                $table->string('role')->default('receptionist')->after('phone');
            }

            if (!Schema::hasColumn('staff', 'department_id')) {
                $table->foreignId('department_id')->nullable()->after('role')->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('staff', 'status')) {
                $table->string('status')->default('active')->after('password');
            }

            if (!Schema::hasColumn('staff', 'remember_token')) {
                $table->rememberToken();
            }
        });

        if (Schema::hasColumn('staff', 'name') && Schema::hasColumn('staff', 'full_name')) {
            DB::table('staff')
                ->whereNull('full_name')
                ->update(['full_name' => DB::raw('name')]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('staff')) {
            return;
        }

        Schema::table('staff', function (Blueprint $table): void {
            foreach (['status', 'department_id', 'role', 'phone', 'full_name'] as $column) {
                if (Schema::hasColumn('staff', $column)) {
                    if ($column === 'department_id') {
                        $table->dropConstrainedForeignId('department_id');
                        continue;
                    }

                    $table->dropColumn($column);
                }
            }
        });
    }
};
