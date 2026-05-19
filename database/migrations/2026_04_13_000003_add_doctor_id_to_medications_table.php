<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('medications')) {
            return;
        }

        Schema::table('medications', function (Blueprint $table) {
            if (!Schema::hasColumn('medications', 'doctor_id')) {
                $table->foreignId('doctor_id')->nullable()->after('patient_id')->constrained()->nullOnDelete();
            }
        });

        $medications = DB::table('medications')->select('id', 'patient_id')->get();

        foreach ($medications as $medication) {
            $doctorId = DB::table('appointments')
                ->where('patient_id', $medication->patient_id)
                ->whereNotNull('doctor_id')
                ->orderByDesc('date')
                ->orderByDesc('time')
                ->value('doctor_id');

            if ($doctorId) {
                DB::table('medications')
                    ->where('id', $medication->id)
                    ->update(['doctor_id' => $doctorId]);
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('medications') || !Schema::hasColumn('medications', 'doctor_id')) {
            return;
        }

        Schema::table('medications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('doctor_id');
        });
    }
};
