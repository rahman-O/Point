<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use App\Search\SearchIndexer;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('search:reindex', function (SearchIndexer $indexer) {
    if (! Schema::hasTable('search_entries')) {
        $this->warn('Skipped: the search_entries table does not exist yet (run migrations first).');

        return 0;
    }

    $this->info('Search index rebuilt: ' . $indexer->rebuild() . ' entries.');
})->purpose('Rebuild the site-wide search index');
