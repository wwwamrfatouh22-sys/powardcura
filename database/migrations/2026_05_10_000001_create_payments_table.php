<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
                $table->string('payment_method');
                $table->string('transaction_id')->nullable()->index();
                $table->string('reference_number')->unique();
                $table->decimal('amount', 10, 2);
                $table->string('status')->default('pending')->index();
                $table->json('gateway_response')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'payment_receipt_path')) {
                $table->dropColumn('payment_receipt_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (! Schema::hasColumn('appointments', 'payment_receipt_path')) {
                $table->string('payment_receipt_path')->nullable()->after('payment_status');
            }
        });

        Schema::dropIfExists('payments');
    }
};
