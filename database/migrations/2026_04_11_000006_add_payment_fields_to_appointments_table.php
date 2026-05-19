<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'payment_amount')) {
                $table->decimal('payment_amount', 10, 2)->nullable()->after('payment_method');
            }

            if (!Schema::hasColumn('appointments', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('payment_amount');
            }

        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'payment_status')) {
                $table->dropColumn('payment_status');
            }

            if (Schema::hasColumn('appointments', 'payment_amount')) {
                $table->dropColumn('payment_amount');
            }
        });
    }
};
