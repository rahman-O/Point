<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sessions_programs', function (Blueprint $table) {
            // Optional master of ceremonies (العريف) among the item's speakers, like the other roles.
            $table->foreignId('mc_id')->nullable()->after('lecturer_id')->constrained('speakers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sessions_programs', function (Blueprint $table) {
            $table->dropForeign(['mc_id']);
            $table->dropColumn('mc_id');
        });
    }
};
