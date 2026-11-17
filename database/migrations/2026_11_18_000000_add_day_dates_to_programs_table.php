<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            // Optional calendar date of each conference day, shown under the day tab on the
            // public agenda. Older programs may stay without dates.
            foreach (range(1, 5) as $n) {
                $table->date("day{$n}_date")->nullable()->after('day5');
            }
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(array_map(fn (int $n) => "day{$n}_date", range(1, 5)));
        });
    }
};
