<?php

use App\Enums\FooterSection;
use App\Filament\Resources\OrganizersResource\Pages\ListOrganizers;
use App\Filament\Resources\PartnersResource\Pages\ListPartners;
use App\Models\Organizers;
use App\Models\Partners;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

beforeEach(function () {
    Organizers::create(['name' => 'Tawasoul', 'image' => 'tawasoul.png']);
    Partners::create(['name' => 'Partner A', 'image' => 'partner-a.png']);
});

it('shows both footer sections by default', function () {
    $this->getJson('/api/footer')
        ->assertOk()
        ->assertJsonPath('organizers.visible', true)
        ->assertJsonPath('organizers.items.0.name', 'Tawasoul')
        ->assertJsonPath('organizers.items.0.image', 'tawasoul.png')
        ->assertJsonPath('partners.visible', true)
        ->assertJsonPath('partners.items.0.name', 'Partner A');
});

it('hides only the section that was turned off', function () {
    FooterSection::Partners->setVisible(false);

    $this->getJson('/api/footer')
        ->assertOk()
        ->assertJsonPath('partners', ['visible' => false, 'items' => []])
        ->assertJsonPath('organizers.visible', true)
        ->assertJsonCount(1, 'organizers.items');

    // The public listing endpoints are not affected by the footer setting.
    $this->getJson('/api/partners')->assertOk()->assertJsonCount(1);
});

it('keeps both sections visible until the settings table is migrated', function () {
    Schema::drop('site_settings');

    $this->getJson('/api/footer')
        ->assertOk()
        ->assertJsonPath('organizers.visible', true)
        ->assertJsonPath('partners.visible', true);
});

it('hides and shows a section from its admin list page', function (string $page, FooterSection $section, FooterSection $other) {
    $this->actingAs(User::factory()->create());

    Livewire::test($page)
        ->assertSee('Shown in the site footer')
        ->assertActionVisible('hideFromFooter')
        ->assertActionHidden('showInFooter')
        ->callAction('hideFromFooter')
        ->assertNotified()
        ->assertSee('Hidden from the site footer')
        ->assertActionHidden('hideFromFooter')
        ->assertActionVisible('showInFooter');

    expect($section->isVisible())->toBeFalse()
        ->and($other->isVisible())->toBeTrue();

    Livewire::test($page)
        ->callAction('showInFooter')
        ->assertActionVisible('hideFromFooter');

    expect($section->isVisible())->toBeTrue()
        ->and($other->isVisible())->toBeTrue();
})->with([
    'partners' => [ListPartners::class, FooterSection::Partners, FooterSection::Organizers],
    'organizers' => [ListOrganizers::class, FooterSection::Organizers, FooterSection::Partners],
]);

it('does not flip the state when clicked from an outdated admin page', function () {
    $this->actingAs(User::factory()->create());

    $outdatedPage = Livewire::test(ListPartners::class)->assertActionVisible('hideFromFooter');

    // Meanwhile, the section gets hidden from another browser tab.
    FooterSection::Partners->setVisible(false);

    $outdatedPage->call('mountAction', 'hideFromFooter');

    expect(FooterSection::Partners->isVisible())->toBeFalse();
});
