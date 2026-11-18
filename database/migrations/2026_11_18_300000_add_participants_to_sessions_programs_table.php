<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sessions_programs', function (Blueprint $table) {
            // Optional stand-ins for speakers: people shown on the agenda with a name, photo
            // and optional description but no speaker profile. JSON list; null = use speakers.
            $table->json('participants')->nullable()->after('lecturer_id');
        });
    }

    public function down(): void
    {
        Schema::table('sessions_programs', function (Blueprint $table) {
            $table->dropColumn('participants');
        });
    }
};
