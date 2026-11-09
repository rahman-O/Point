<?php

use App\Filament\Resources\PartnersResource\Pages\ListPartners;
use App\Models\Partners;
use App\Models\User;
use Livewire\Livewire;

it('lists footer partners in the order admins arranged them', function () {
    $second = Partners::create(['name' => 'Second', 'image' => 'b.png', 'sort_order' => 2]);
    $first = Partners::create(['name' => 'First', 'image' => 'a.png', 'sort_order' => 1]);
    $third = Partners::create(['name' => 'Third', 'image' => 'c.png', 'sort_order' => 3]);

    $this->getJson('/api/footer')
        ->assertOk()
        ->assertJsonPath('partners.items.*.id', [$first->id, $second->id, $third->id]);
});

it('adds new partners to the end of the footer', function () {
    Partners::create(['name' => 'A', 'image' => 'a.png', 'sort_order' => 5]);
    Partners::create(['name' => 'B', 'image' => 'b.png', 'sort_order' => 2]);

    $new = Partners::create(['name' => 'New', 'image' => 'new.png']);

    expect($new->sort_order)->toBe(6)
        ->and(Partners::ordered()->pluck('name')->all())->toBe(['B', 'A', 'New']);
});

it('lets admins drag partners into a new order from the list page', function () {
    $this->actingAs(User::factory()->create());

    [$a, $b, $c] = collect(['A', 'B', 'C'])
        ->map(fn (string $name) => Partners::create(['name' => $name, 'image' => "$name.png"]))
        ->all();

    Livewire::test(ListPartners::class)
        ->assertCanSeeTableRecords([$a, $b, $c], inOrder: true)
        ->call('reorderTable', [$c->id, $a->id, $b->id])
        ->assertCanSeeTableRecords([$c, $a, $b], inOrder: true);

    expect(Partners::ordered()->pluck('name')->all())->toBe(['C', 'A', 'B']);

    $this->getJson('/api/footer')
        ->assertJsonPath('partners.items.*.name', ['C', 'A', 'B']);
});
