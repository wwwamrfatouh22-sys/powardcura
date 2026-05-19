<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->replaceForeign('doctors', 'department_id', 'departments', 'restrict');
        $this->replaceForeign('appointments', 'doctor_id', 'doctors', 'restrict');
        $this->replaceForeign('admin_appointments', 'doctor_id', 'doctors', 'restrict');
        $this->replaceForeign('admin_appointments', 'patient_id', 'patients', 'restrict');
        $this->replaceForeign('document_signatures', 'doctor_id', 'doctors', 'restrict');
        $this->replaceForeign('leave_requests', 'doctor_id', 'doctors', 'restrict');
        $this->replaceForeign('medical_positions', 'department_id', 'departments', 'restrict');
        $this->replaceForeign('private_clinics', 'doctor_id', 'doctors', 'restrict');
        $this->replaceForeign('ratings', 'doctor_id', 'doctors', 'restrict');
        $this->replaceForeign('ratings', 'appointment_id', 'appointments', 'null');
        $this->replaceForeign('reports', 'department_id', 'departments', 'restrict');
        $this->replaceForeign('reports', 'doctor_id', 'doctors', 'restrict');
        $this->replaceForeign('staff_leave_requests', 'doctor_id', 'doctors', 'restrict');
    }

    public function down(): void
    {
        $this->replaceForeign('doctors', 'department_id', 'departments', 'cascade');
        $this->replaceForeign('appointments', 'doctor_id', 'doctors', 'cascade');
        $this->replaceForeign('admin_appointments', 'doctor_id', 'doctors', 'cascade');
        $this->replaceForeign('admin_appointments', 'patient_id', 'patients', 'cascade');
        $this->replaceForeign('document_signatures', 'doctor_id', 'doctors', 'cascade');
        $this->replaceForeign('leave_requests', 'doctor_id', 'doctors', 'cascade');
        $this->replaceForeign('medical_positions', 'department_id', 'departments', 'cascade');
        $this->replaceForeign('private_clinics', 'doctor_id', 'doctors', 'cascade');
        $this->replaceForeign('ratings', 'doctor_id', 'doctors', 'cascade');
        $this->replaceForeign('ratings', 'appointment_id', 'appointments', 'cascade');
        $this->replaceForeign('reports', 'department_id', 'departments', 'cascade');
        $this->replaceForeign('reports', 'doctor_id', 'doctors', 'cascade');
        $this->replaceForeign('staff_leave_requests', 'doctor_id', 'doctors', 'cascade');
    }

    private function replaceForeign(string $table, string $column, string $references, string $deleteRule): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        $constraint = $this->foreignConstraintName($table, $column, $references);

        if ($constraint !== null) {
            Schema::table($table, function (Blueprint $table) use ($constraint): void {
                $table->dropForeign($constraint);
            });
        }

        Schema::table($table, function (Blueprint $table) use ($column, $references, $deleteRule): void {
            $foreign = $table->foreign($column)->references('id')->on($references);

            match ($deleteRule) {
                'cascade' => $foreign->cascadeOnDelete(),
                'null' => $foreign->nullOnDelete(),
                default => $foreign->restrictOnDelete(),
            };
        });
    }

    private function foreignConstraintName(string $table, string $column, string $references): ?string
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return $table . '_' . $column . '_foreign';
        }

        $rows = DB::select(
            <<<'SQL'
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE CONSTRAINT_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
              AND REFERENCED_TABLE_NAME = ?
            LIMIT 1
            SQL,
            [$table, $column, $references]
        );

        return $rows[0]->CONSTRAINT_NAME ?? null;
    }
};
