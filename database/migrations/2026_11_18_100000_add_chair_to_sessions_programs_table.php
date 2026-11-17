<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sessions_programs', function (Blueprint $table) {
            // Optional session chair among the item's speakers, like the other roles.
            $table->foreignId('chair_id')->nullable()->after('trainer_id')->constrained('speakers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sessions_programs', function (Blueprint $table) {
            $table->dropForeign(['chair_id']);
            $table->dropColumn('chair_id');
        });
    }
};
