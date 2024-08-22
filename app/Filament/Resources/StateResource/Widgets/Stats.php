<?php

namespace App\Filament\Resources\StateResource\Widgets;

use App\Models\News;
use App\Models\Programs;
use App\Models\SessionsProgram;
use App\Models\Speakers;
use App\Models\Stream;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Stats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Speakers', Speakers::count())
                ->chart([4, 17, 5, 12, 8, 14, 2])
                ->color('success'),
            Stat::make('Sessions', SessionsProgram::count())
                ->chart([10, 3, 16, 7, 19, 2, 13])
                ->color('warning'),
            Stat::make('Programs', Programs::count())
                ->chart([6, 18, 9, 4, 15, 3, 20])
                ->color('danger'),
            Stat::make('Streams', Stream::count())
                ->chart([11, 5, 14, 2, 19, 8, 7])
                ->color('warning'),
            Stat::make('News', News::count())
                ->chart([9, 1, 13, 6, 17, 4, 15])
                ->color('success'),



        ];
    }

}
