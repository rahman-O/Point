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
