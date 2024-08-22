<?php

use App\Models\SessionsProgram;
use App\Models\Speakers;
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
        Schema::create('session_speaker', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SessionsProgram::class);
            $table->foreignIdFor(Speakers::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_speaker');
    }
};
