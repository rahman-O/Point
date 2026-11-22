<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * A session can have several masters of ceremonies (العريف), so the single mc_id column
 * becomes a pivot table. Any MC already assigned is carried over.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_mc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sessions_program_id')->constrained('sessions_programs')->cascadeOnDelete();
            $table->foreignId('speakers_id')->constrained('speakers')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['sessions_program_id', 'speakers_id']);
        });

        $now = now();
        DB::table('sessions_programs')
            ->whereNotNull('mc_id')
            ->orderBy('id')
            ->get(['id', 'mc_id'])
            ->each(fn ($row) => DB::table('session_mc')->insert([
                'sessions_program_id' => $row->id,
                'speakers_id' => $row->mc_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]));

        Schema::table('sessions_programs', function (Blueprint $table) {
            $table->dropForeign(['mc_id']);
            $table->dropColumn('mc_id');
        });
    }

    public function down(): void
    {
        Schema::table('sessions_programs', function (Blueprint $table) {
            $table->foreignId('mc_id')->nullable()->after('lecturer_id')->constrained('speakers')->nullOnDelete();
        });

        // Only the first MC of each session fits back into the single column.
        DB::table('session_mc')
            ->orderBy('id')
            ->get(['sessions_program_id', 'speakers_id'])
            ->groupBy('sessions_program_id')
            ->each(fn ($rows, $sessionId) => DB::table('sessions_programs')
                ->where('id', $sessionId)
                ->update(['mc_id' => $rows->first()->speakers_id]));

        Schema::dropIfExists('session_mc');
    }
};
