<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sessions_programs', function (Blueprint $table) {
            // Optional roles among the item's speakers, like facilitator_id.
            $table->foreignId('rapporteur_id')->nullable()->after('facilitator_id')->constrained('speakers')->nullOnDelete();
            $table->foreignId('trainer_id')->nullable()->after('rapporteur_id')->constrained('speakers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sessions_programs', function (Blueprint $table) {
            $table->dropForeign(['rapporteur_id']);
            $table->dropForeign(['trainer_id']);
            $table->dropColumn(['rapporteur_id', 'trainer_id']);
        });
    }
};
