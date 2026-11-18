<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sessions_programs', function (Blueprint $table) {
            // Optional lecturer among the item's speakers, like the other roles.
            $table->foreignId('lecturer_id')->nullable()->after('chair_id')->constrained('speakers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sessions_programs', function (Blueprint $table) {
            $table->dropForeign(['lecturer_id']);
            $table->dropColumn('lecturer_id');
        });
    }
};
