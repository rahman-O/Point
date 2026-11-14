<?php

use App\Filament\Resources\SpeakersResource\Pages\CreateSpeakers;
use App\Models\Speakers;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());
});

function speakerForm(UploadedFile $photo): array
{
    return [
        'name_en' => 'Sara',
        'name_ar' => 'سارة',
        'job_en' => 'Journalist',
        'job_ar' => 'صحفية',
        'country_en' => 'Iraq',
        'country_ar' => 'العراق',
        'years' => ['2026'],
        'desc_en' => 'Bio',
        'desc_ar' => 'نبذة',
        'image' => $photo,
    ];
}

it('rejects speaker photos too small to show sharply', function () {
    Livewire::test(CreateSpeakers::class)
        ->fillForm(speakerForm(UploadedFile::fake()->image('tiny.jpg', 300, 375)))
        ->call('create')
        ->assertHasFormErrors(['image']);

    expect(Speakers::count())->toBe(0);
});

it('accepts a speaker photo of the required size', function () {
    Livewire::test(CreateSpeakers::class)
        ->fillForm(speakerForm(UploadedFile::fake()->image('portrait.jpg', 800, 1000)))
        ->call('create')
        ->assertHasNoFormErrors();

    Storage::disk('public')->assertExists(Speakers::sole()->image);
});

it('lets admins set a speaker’s position in the public lists', function () {
    $speaker = Speakers::create([
        ...collect(speakerForm(UploadedFile::fake()->image('x.jpg', 800, 1000)))->except(['years', 'image'])->all(),
        'year' => '2026',
        'image' => 'sara.png',
    ]);
    Storage::disk('public')->put('sara.png', UploadedFile::fake()->image('sara.png', 800, 1000)->getContent());

    Livewire::test(\App\Filament\Resources\SpeakersResource\Pages\EditSpeakers::class, ['record' => $speaker->getKey()])
        ->assertFormSet(['priority' => 0])
        ->fillForm(['priority' => -1])
        ->call('save')
        ->assertHasFormErrors(['priority'])
        ->fillForm(['priority' => 3])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($speaker->fresh()->priority)->toBe(3);
});
