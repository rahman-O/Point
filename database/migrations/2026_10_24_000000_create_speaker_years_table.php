<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('speaker_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('speakers_id')->constrained('speakers')->cascadeOnDelete();
            $table->string('year', 4);
            $table->timestamps();

            $table->unique(['speakers_id', 'year']);
            $table->index('year');
        });

        // Copy each speaker's current single year; speakers.year itself is left untouched.
        DB::table('speakers')->select('id', 'year')->orderBy('id')->chunkById(500, function ($speakers) {
            $now = now();
            $rows = $speakers
                ->filter(fn ($speaker) => preg_match('/^\d{4}$/', trim((string) $speaker->year)))
                ->map(fn ($speaker) => [
                    'speakers_id' => $speaker->id,
                    'year' => trim($speaker->year),
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
                ->values()
                ->all();

            DB::table('speaker_years')->insertOrIgnore($rows);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('speaker_years');
    }
};
