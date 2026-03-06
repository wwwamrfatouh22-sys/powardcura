<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('training_programs', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->integer('age');
            $table->string('gender');

            $table->string('phone');

            $table->string('university');

            $table->foreignId('department_id')->constrained()->cascadeOnDelete();

            $table->string('cv');

            $table->decimal('gpa',3,1);

            $table->enum('status',['pending','approved','rejected'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_programs');
    }
};
