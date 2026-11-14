<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('speakers', function (Blueprint $table) {
            // Position in the public speaker lists; 1 shows first. 0 (the default) means
            // "no fixed position": the speaker follows the numbered ones, newest first.
            $table->unsignedSmallInteger('priority')->default(0)->after('image')->index();
        });
    }

    public function down(): void
    {
        Schema::table('speakers', function (Blueprint $table) {
            $table->dropIndex(['priority']);
            $table->dropColumn('priority');
        });
    }
};
