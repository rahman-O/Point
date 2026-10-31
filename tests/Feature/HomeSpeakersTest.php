<?php

use App\Models\Speakers;

function speakerIn(array $years, string $name, string $createdAt): Speakers
{
    $speaker = Speakers::create([
        'name_en' => $name,
        'name_ar' => $name,
        'job_en' => 'Journalist',
        'job_ar' => 'صحفي',
        'country_en' => 'Iraq',
        'country_ar' => 'العراق',
        'year' => max($years),
        'desc_en' => 'Bio',
        'desc_ar' => 'نبذة',
        'image' => 'speaker.png',
    ]);
    $speaker->syncYears($years);
    $speaker->forceFill(['created_at' => $createdAt])->saveQuietly();

    return $speaker;
}

it('shows only the speakers of the newest year on the home page, newest first', function () {
    speakerIn(['2025'], 'Last year', '2025-09-01 10:00:00');
    speakerIn(['2025', '2026'], 'Returning', '2025-09-02 10:00:00');
    speakerIn(['2026'], 'New', '2026-10-28 10:00:00');
    speakerIn(['2026'], 'Newest', '2026-10-31 10:00:00');

    $this->getJson('/api/top/speakers')
        ->assertOk()
        ->assertJsonPath('year', '2026')
        ->assertJsonPath('speakers.*.name_en', ['Newest', 'New', 'Returning']);
});

it('shows at most 18 speakers on the home page (three rows)', function () {
    foreach (range(1, 20) as $day) {
        speakerIn(['2026'], "Speaker $day", sprintf('2026-10-%02d 10:00:00', $day));
    }

    $names = $this->getJson('/api/top/speakers')->assertOk()->json('speakers.*.name_en');

    expect($names)->toHaveCount(18)
        ->and($names[0])->toBe('Speaker 20')
        ->and($names[17])->toBe('Speaker 3');
});

it('shows no speakers on the home page when there are none', function () {
    $this->getJson('/api/top/speakers')
        ->assertOk()
        ->assertJsonPath('year', null)
        ->assertJsonPath('speakers', []);
});
