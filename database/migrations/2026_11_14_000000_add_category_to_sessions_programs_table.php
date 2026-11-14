<?php

use App\Enums\SessionCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sessions_programs', function (Blueprint $table) {
            $table->string('category', 32)->default(SessionCategory::DEFAULT->value)->after('day')->index();
        });

        // Items created before categories existed are sorted by the words in their titles
        // (workshop / forum / meeting); everything else stays a plain session. Admins can
        // correct any of them from the dashboard.
        DB::table('sessions_programs')
            ->select(['id', 'title_en', 'title_ar'])
            ->orderBy('id')
            ->each(function (object $row): void {
                $category = SessionCategory::guessFromTitle($row->title_en, $row->title_ar);
                if ($category !== SessionCategory::DEFAULT) {
                    DB::table('sessions_programs')->where('id', $row->id)->update(['category' => $category->value]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('sessions_programs', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn('category');
        });
    }
};
