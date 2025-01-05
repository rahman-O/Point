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
            // Make columns nullable
            $table->string('presentation_en')->nullable()->change();
            $table->string('presentation_ar')->nullable()->change();
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();
            // Rename columns
            $table->renameColumn('sub_en', 'subject_en');
            $table->renameColumn('sub_ar', 'subject_ar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions_programs', function (Blueprint $table) {
            // Revert nullable changes
            $table->string('presentation_en')->nullable(false)->change();
            $table->string('presentation_ar')->nullable(false)->change();
            $table->dropColumn('title_en');
            $table->dropColumn('title_ar');

            // Revert column names
            $table->renameColumn('subject_en', 'sub_en');
            $table->renameColumn('subject_ar', 'sub_ar');
        });
    }
};
