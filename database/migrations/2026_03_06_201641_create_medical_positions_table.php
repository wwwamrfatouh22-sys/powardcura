<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('medical_positions', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->integer('age');

            $table->enum('gender', ['male','female']);

            $table->string('phone');

            $table->foreignId('department_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('cv');

            $table->enum('status',['pending','approved','rejected'])
                ->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_positions');
    }
};
