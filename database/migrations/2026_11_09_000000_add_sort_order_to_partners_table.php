<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('image')->index();
        });

        // Existing partners keep the order they were shown in so far (oldest first).
        DB::table('partners')->update(['sort_order' => DB::raw('id')]);
    }

    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
            $table->dropColumn('sort_order');
        });
    }
};
