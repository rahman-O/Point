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
        Schema::create('sessions_program', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('day');
            $table->string('year');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('sub_en');
            $table->string('sub_ar');
            $table->string('pres_en');
            $table->string('pres_ar');
            //speaker
            $table->integer('speaker_id');
            //FACILITORS
            $table->integer('facilitator_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
