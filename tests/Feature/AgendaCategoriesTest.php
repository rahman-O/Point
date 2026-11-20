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
        ->assertJsonPath('categories.3.label_ar', 'الملتقيات الوطنية')
        ->assertJsonPath('categories.3.empty_ar', 'لا توجد ملتقيات وطنية مجدولة في هذا اليوم.')
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

it('lets admins number parallel items so a chosen one shows first', function () {
    $this->actingAs(User::factory()->create());
    $program = Programs::create(['year' => (string) now()->year, 'day1' => 1]);
    $first = agendaItem($program, 'Podcast workshop', ['start_time' => '14:00', 'end_time' => '17:00']);
    $second = agendaItem($program, 'Algorithms workshop', ['start_time' => '14:00', 'end_time' => '17:00']);

    Livewire::test(SessionsProgramRelationManager::class, [
        'ownerRecord' => $program,
        'pageClass' => EditPrograms::class,
    ])
        ->mountTableAction('edit', $second)
        ->assertTableActionDataSet(['priority' => 0])
        ->setTableActionData(['priority' => 1])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors()
        ->mountTableAction('edit', $first)
        ->setTableActionData(['priority' => -1])
        ->callMountedTableAction()
        ->assertHasTableActionErrors(['priority']);

    $this->getJson('/api/programs/current/year')
        ->assertOk()
        ->assertJsonPath('sessions_program.0.priority', 0)
        ->assertJsonPath('sessions_program.1.priority', 1);
});

it('filters agenda items by day and type together in the admin table', function () {
    $this->actingAs(User::factory()->create());
    $program = Programs::create(['year' => '2026', 'day1' => 1, 'day2' => 1]);
    $d1Session = agendaItem($program, 'Opening day 1');
    $d1Workshop = agendaItem($program, 'Workshop day 1', ['category' => SessionCategory::Workshops]);
    $d2Workshop = agendaItem($program, 'Workshop day 2', ['day' => 'day2', 'category' => SessionCategory::Workshops]);

    Livewire::test(SessionsProgramRelationManager::class, [
        'ownerRecord' => $program,
        'pageClass' => EditPrograms::class,
    ])
        ->assertCanSeeTableRecords([$d1Session, $d1Workshop, $d2Workshop])
        ->filterTable('day', 'day1')
        ->assertCanSeeTableRecords([$d1Session, $d1Workshop])
        ->assertCanNotSeeTableRecords([$d2Workshop])
        ->filterTable('category', 'workshops')
        ->assertCanSeeTableRecords([$d1Workshop])
        ->assertCanNotSeeTableRecords([$d1Session, $d2Workshop]);
});

function agendaSpeaker(string $name): \App\Models\Speakers
{
    return \App\Models\Speakers::create([
        'name_en' => $name, 'name_ar' => $name, 'job_en' => 'J', 'job_ar' => 'ج',
        'country_en' => 'Iraq', 'country_ar' => 'العراق', 'year' => '2026',
        'desc_en' => 'b', 'desc_ar' => 'ب', 'image' => 'x.png',
    ]);
}

it('lets admins assign a rapporteur, a trainer, a chair, a lecturer and an MC among the item’s speakers', function () {
    $this->actingAs(User::factory()->create());
    $program = Programs::create(['year' => (string) now()->year, 'day1' => 1]);
    $item = agendaItem($program, 'Workshop');
    [$a, $b, $c, $d, $e, $f] = [agendaSpeaker('A'), agendaSpeaker('B'), agendaSpeaker('C'), agendaSpeaker('D'), agendaSpeaker('E'), agendaSpeaker('F')];
    $item->speakers()->sync([$a->id, $b->id, $c->id, $d->id, $e->id, $f->id]);

    $manager = Livewire::test(SessionsProgramRelationManager::class, [
        'ownerRecord' => $program,
        'pageClass' => EditPrograms::class,
    ]);

    $manager->mountTableAction('edit', $item)
        ->setTableActionData(['facilitator_id' => $a->id, 'rapporteur_id' => $b->id, 'trainer_id' => $c->id, 'chair_id' => $d->id, 'lecturer_id' => $e->id, 'mc_id' => $f->id])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($item->fresh())->rapporteur_id->toBe($b->id)->trainer_id->toBe($c->id)->facilitator_id->toBe($a->id)->chair_id->toBe($d->id)->lecturer_id->toBe($e->id)->mc_id->toBe($f->id);

    $this->getJson('/api/programs/current/year')
        ->assertJsonPath('sessions_program.0.rapporteur_id', $b->id)
        ->assertJsonPath('sessions_program.0.trainer_id', $c->id)
        ->assertJsonPath('sessions_program.0.rapporteur.name_en', 'B')
        ->assertJsonPath('sessions_program.0.trainer.name_en', 'C')
        ->assertJsonPath('sessions_program.0.chair_id', $d->id)
        ->assertJsonPath('sessions_program.0.chair.name_en', 'D')
        ->assertJsonPath('sessions_program.0.lecturer.name_en', 'E')
        ->assertJsonPath('sessions_program.0.mc.name_en', 'F');

    // A speaker holding a role cannot be deleted while the item references them.
    $item->speakers()->sync([$a->id]);
    expect($b->isUsedInAgenda())->toBeTrue()
        ->and($c->isUsedInAgenda())->toBeTrue();

    expect($d->isUsedInAgenda())->toBeTrue()->and($e->isUsedInAgenda())->toBeTrue()->and($f->isUsedInAgenda())->toBeTrue();
    $item->update(['rapporteur_id' => null, 'trainer_id' => null, 'chair_id' => null, 'lecturer_id' => null, 'mc_id' => null]);
    expect($b->isUsedInAgenda())->toBeFalse()->and($d->isUsedInAgenda())->toBeFalse()->and($e->isUsedInAgenda())->toBeFalse()->and($f->isUsedInAgenda())->toBeFalse();
});

it('lists agenda items chronologically, so an item moved earlier climbs up', function () {
    $program = Programs::create(['year' => (string) now()->year, 'day1' => 1]);
    agendaItem($program, 'Opening', ['start_time' => '09:00', 'end_time' => '09:30']);
    $session = agendaItem($program, 'Session one', ['start_time' => '10:00', 'end_time' => '11:00']);
    $music = agendaItem($program, 'Music', ['start_time' => '11:00', 'end_time' => '11:30']);

    $this->getJson('/api/programs/current/year')
        ->assertJsonPath('sessions_program.*.title_en', ['Opening', 'Session one', 'Music']);

    // Rescheduled earlier: it moves above the session even though it was added later.
    $music->update(['start_time' => '09:30', 'end_time' => '10:00']);

    $this->getJson('/api/programs/current/year')
        ->assertJsonPath('sessions_program.*.title_en', ['Opening', 'Music', 'Session one']);

    // The admin table can still be sorted by hand (here: newest first).
    $this->actingAs(User::factory()->create());
    Livewire::test(SessionsProgramRelationManager::class, ['ownerRecord' => $program, 'pageClass' => EditPrograms::class])
        ->sortTable('priority', 'desc')
        ->assertCanSeeTableRecords([$music, $session], inOrder: true);
});

it('exposes the optional date of each enabled day to the agenda page', function () {
    $program = Programs::create(['year' => (string) now()->year, 'day1' => 1, 'day2' => 1, 'day2_date' => '2026-11-25']);
    agendaItem($program, 'Opening');

    $this->getJson('/api/programs/current/year')
        ->assertOk()
        ->assertJsonPath('day_dates', ['day1' => null, 'day2' => '2026-11-25']);
});

it('lets admins date the days of a program, in order, and leave old ones undated', function () {
    $this->actingAs(User::factory()->create());
    $program = Programs::create(['year' => '2026', 'day1' => 1, 'day2' => 1]);

    Livewire::test(\App\Filament\Resources\ProgramsResource\Pages\EditPrograms::class, ['record' => $program->getKey()])
        ->assertFormSet(['day1_date' => null, 'day2_date' => null])
        ->assertFormFieldIsVisible('day2_date')
        ->assertFormFieldIsHidden('day3_date')
        ->fillForm(['day1_date' => '2026-11-25', 'day2_date' => '2026-11-24'])
        ->call('save')
        ->assertHasFormErrors(['day2_date'])
        ->fillForm(['day1_date' => '2026-11-24', 'day2_date' => '2026-11-25'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($program->fresh()->day_dates)->toBe(['day1' => '2026-11-24', 'day2' => '2026-11-25']);

    // Dates stay optional: clearing them is fine.
    Livewire::test(\App\Filament\Resources\ProgramsResource\Pages\EditPrograms::class, ['record' => $program->getKey()])
        ->fillForm(['day1_date' => null, 'day2_date' => null])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($program->fresh()->day_dates)->toBe(['day1' => null, 'day2' => null]);
});

it('lets admins list custom participants instead of speakers, shown without profile links', function () {
    $this->actingAs(User::factory()->create());
    \Illuminate\Support\Facades\Storage::fake('public');
    $program = Programs::create(['year' => (string) now()->year, 'day1' => 1]);
    $item = agendaItem($program, 'Panel');
    $speaker = agendaSpeaker('A');
    $item->speakers()->sync([$speaker->id]);
    $item->update(['facilitator_id' => $speaker->id]);

    $manager = Livewire::test(SessionsProgramRelationManager::class, [
        'ownerRecord' => $program,
        'pageClass' => EditPrograms::class,
    ]);

    // Switching to participants: a photo and both names are required, description is not.
    $manager->mountTableAction('edit', $item)
        ->assertTableActionDataSet(['people_mode' => 'speakers'])
        ->setTableActionData([
            'people_mode' => 'participants',
            'participants' => [
                ['name_en' => 'Guest One', 'name_ar' => 'ضيف أول', 'desc_en' => 'Moderator', 'desc_ar' => 'مُحاور', 'image' => ['u1' => \Illuminate\Http\UploadedFile::fake()->image('g1.jpg', 400, 400)]],
                ['name_en' => 'Guest Two', 'name_ar' => 'ضيف ثانٍ', 'desc_en' => null, 'desc_ar' => null, 'image' => ['u2' => \Illuminate\Http\UploadedFile::fake()->image('g2.jpg', 400, 400)]],
            ],
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    $item->refresh();
    expect($item->participants)->toHaveCount(2)
        ->and($item->participants[0]['name_ar'])->toBe('ضيف أول')
        ->and($item->participants[1]['desc_en'])->toBeNull()
        ->and($item->participants[0]['image'])->toBeString()
        ->and($item->speakers)->toHaveCount(0)
        ->and($item->facilitator_id)->toBeNull();
    \Illuminate\Support\Facades\Storage::disk('public')->assertExists($item->participants[0]['image']);

    $this->getJson('/api/programs/current/year')
        ->assertJsonPath('sessions_program.0.participants.1.name_en', 'Guest Two')
        ->assertJsonCount(0, 'sessions_program.0.speakers');

    // The edit form reopens in participants mode; switching back to speakers drops them.
    $manager->mountTableAction('edit', $item)
        ->assertTableActionDataSet(['people_mode' => 'participants'])
        ->setTableActionData(['people_mode' => 'speakers', 'speakers' => [$speaker->id]])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    $item->refresh();
    expect($item->participants)->toBeNull()
        ->and($item->speakers->pluck('id')->all())->toBe([$speaker->id]);
});

it('rejects a custom participant without a name or photo', function () {
    $this->actingAs(User::factory()->create());
    \Illuminate\Support\Facades\Storage::fake('public');
    $program = Programs::create(['year' => '2026', 'day1' => 1]);
    $item = agendaItem($program, 'Panel');

    Livewire::test(SessionsProgramRelationManager::class, ['ownerRecord' => $program, 'pageClass' => EditPrograms::class])
        ->mountTableAction('edit', $item)
        ->setTableActionData([
            'people_mode' => 'participants',
            'participants' => [['name_en' => '', 'name_ar' => 'بلا اسم', 'image' => null]],
        ])
        ->callMountedTableAction()
        ->assertHasTableActionErrors(['participants.0.name_en', 'participants.0.image']);

    expect($item->fresh()->participants)->toBeNull();
});
