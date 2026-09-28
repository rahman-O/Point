<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_type', 20);
            $table->unsignedBigInteger('entry_id');
            $table->string('title_en', 500)->nullable();
            $table->string('title_ar', 500)->nullable();
            $table->string('subtitle_en', 500)->nullable();
            $table->string('subtitle_ar', 500)->nullable();
            $table->mediumText('content_en')->nullable();
            $table->mediumText('content_ar')->nullable();
            $table->text('search_title');
            $table->text('search_meta')->nullable();
            $table->mediumText('search_all');
            $table->string('image', 500)->nullable();
            $table->string('url', 1000);
            $table->string('year', 10)->nullable();
            $table->date('published_on')->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();

            $table->unique(['entry_type', 'entry_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_entries');
    }
};
