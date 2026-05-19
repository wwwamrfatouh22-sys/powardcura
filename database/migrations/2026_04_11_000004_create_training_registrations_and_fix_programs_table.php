<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('training_programs')) {
            Schema::create('training_programs', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->unsignedInteger('duration_weeks')->nullable();
                $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        } else {
            Schema::table('training_programs', function (Blueprint $table) {
                if (!Schema::hasColumn('training_programs', 'title')) {
                    $table->string('title')->nullable()->after('id');
                }

                if (!Schema::hasColumn('training_programs', 'description')) {
                    $table->text('description')->nullable()->after('title');
                }

                if (!Schema::hasColumn('training_programs', 'duration_weeks')) {
                    $table->unsignedInteger('duration_weeks')->nullable()->after('description');
                }

                if (!Schema::hasColumn('training_programs', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('department_id');
                }
            });

            if (Schema::hasColumn('training_programs', 'name')) {
                DB::table('training_programs')
                    ->whereNull('title')
                    ->whereNotNull('name')
                    ->update([
                        'title' => DB::raw('name'),
                    ]);
            }

            DB::table('training_programs')
                ->whereNull('title')
                ->update([
                    'title' => 'General Training Program',
                ]);
        }

        if (!Schema::hasTable('training_registrations')) {
            Schema::create('training_registrations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('training_id')->constrained('training_programs')->cascadeOnDelete();
                $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
                $table->string('full_name');
                $table->string('email');
                $table->string('phone', 30);
                $table->string('national_id', 30);
                $table->unsignedTinyInteger('age')->nullable();
                $table->enum('gender', ['male', 'female'])->nullable();
                $table->string('university')->nullable();
                $table->decimal('gpa', 3, 2)->nullable();
                $table->string('cv_path');
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->timestamps();
            });
        }

        if (DB::table('training_programs')->count() === 0) {
            DB::table('training_programs')->insert([
                [
                    'title' => 'Clinical Internship Program',
                    'description' => 'Hands-on clinical rotation for final-year medical students.',
                    'duration_weeks' => 12,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title' => 'Nursing Residency Program',
                    'description' => 'Structured nursing practice and mentorship in core hospital units.',
                    'duration_weeks' => 16,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title' => 'Radiology Observership',
                    'description' => 'Supervised radiology exposure with case-based training.',
                    'duration_weeks' => 8,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('training_registrations')) {
            Schema::drop('training_registrations');
        }

        if (Schema::hasTable('training_programs')) {
            Schema::table('training_programs', function (Blueprint $table) {
                if (Schema::hasColumn('training_programs', 'is_active')) {
                    $table->dropColumn('is_active');
                }

                if (Schema::hasColumn('training_programs', 'duration_weeks')) {
                    $table->dropColumn('duration_weeks');
                }

                if (Schema::hasColumn('training_programs', 'description')) {
                    $table->dropColumn('description');
                }

                if (Schema::hasColumn('training_programs', 'title')) {
                    $table->dropColumn('title');
                }
            });
        }
    }
};
