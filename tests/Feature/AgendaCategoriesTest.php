<?php

use App\Enums\SessionCategory;
use App\Filament\Resources\ProgramsResource\Pages\EditPrograms;
use App\Filament\Resources\ProgramsResource\RelationManagers\SessionsProgramRelationManager;
use App\Models\Programs;
use App\Models\SessionsProgram;
use App\Models\User;
use Livewire\Livewire;

function agendaItem(Programs $program, string $titleAr, array $overrides = []): SessionsProgram
{
    return $program->sessionsProgram()->create([
        'day' => 'day1',
        'year' => $program->year,
        'start_time' => '09:00',
        'end_time' => '10:00',
        'title_en' => $titleAr,
        'title_ar' => $titleAr,
        'subject_en' => 'x',
        'subject_ar' => 'x',
        ...$overrides,
    ]);
}

it('guesses the category of old agenda items from their titles', function (string $title, SessionCategory $expected) {
    expect(SessionCategory::guessFromTitle(null, $title))->toBe($expected);
})->with([
    ['ورشة تدريب اساسيات صناعة البودكاست', SessionCategory::Workshops],
    ['الاجتماع الاقليمي بشأن القيود على حرية عمل الصحافة', SessionCategory::Meetings],
    ['الملتقى الوطني لتحالف الدفاع عن حرية عمل المنظمات', SessionCategory::Forums],
    ['Regional Forum on digital rights', SessionCategory::Forums],
    ['استراحة الغداء', SessionCategory::Sessions],
    ['ما بعد احتجاجات تشرين: إعادة بناء مشروع الإصلاح', SessionCategory::Sessions],
]);

it('sends the agenda sections in display order with each item’s category', function () {
    $program = Programs::create(['year' => (string) now()->year, 'day1' => 1]);
    agendaItem($program, 'Opening');
    agendaItem($program, 'Podcast workshop', ['category' => SessionCategory::Workshops]);

    $this->getJson('/api/programs/current/year')
        ->assertOk()
        ->assertJsonPath('categories.*.key', ['sessions', 'workshops', 'meetings', 'forums'])
        ->assertJsonPath('categories.0.label_ar', 'الجلسات')
        ->assertJsonPath('categories.3.empty_ar', 'لا توجد ملتقيات وطنية أو إقليمية مجدولة في هذا اليوم.')
        ->assertJsonPath('sessions_program.0.category', 'sessions')
        ->assertJsonPath('sessions_program.1.category', 'workshops');
});

it('lets admins pick the agenda section when adding an item', function () {
    $this->actingAs(User::factory()->create());
    $program = Programs::create(['year' => '2026', 'day1' => 1]);

    Livewire::test(SessionsProgramRelationManager::class, [
        'ownerRecord' => $program,
        'pageClass' => EditPrograms::class,
    ])
        ->mountTableAction('create')
        ->assertTableActionDataSet(['category' => 'sessions'])
        ->setTableActionData([
            'day' => 'day1',
            'category' => 'forums',
            'start_time' => '14:00',
            'end_time' => '17:00',
            'title_en' => 'Researchers forum',
            'title_ar' => 'ملتقى الباحثين',
            'subject_en' => 'x',
            'subject_ar' => 'x',
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect(SessionsProgram::sole()->category)->toBe(SessionCategory::Forums);
});
