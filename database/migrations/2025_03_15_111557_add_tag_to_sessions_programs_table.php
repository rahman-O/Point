<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
        {
            Schema::table('sessions_programs', function (Blueprint $table) {
                $table->dropColumn('facilitator_id');
            });

            Schema::table('sessions_programs', function (Blueprint $table) {
                $table->foreignId('facilitator_id')->nullable()->constrained('speakers')->onDelete('set null');
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('sessions_programs', function (Blueprint $table) {
                $table->dropForeign(['facilitator_id']);
                $table->dropColumn('facilitator_id');
                $table->integer('facilitator_id')->nullable();
            });
        }

};
