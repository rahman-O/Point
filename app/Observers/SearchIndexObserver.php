<?php

namespace App\Observers;

use App\Search\SearchIndexer;
use Illuminate\Database\Eloquent\Model;

class SearchIndexObserver
{
    public function saved(Model $model): void
    {
        SearchIndexer::queue($model);
    }

    public function deleted(Model $model): void
    {
        SearchIndexer::queue($model);
    }
}
