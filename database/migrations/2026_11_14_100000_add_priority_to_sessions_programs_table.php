<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sessions_programs', function (Blueprint $table) {
            // Order among items that share a time slot; lower shows first. 0 (the default)
            // keeps the order existing items have had so far.
            $table->unsignedSmallInteger('priority')->default(0)->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('sessions_programs', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
    }
};
