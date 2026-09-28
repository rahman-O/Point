<?php

use App\Search\TextNormalizer;

it('treats Arabic spelling variants as equal', function (string $stored, string $typed) {
    expect(TextNormalizer::normalize($stored))->toBe(TextNormalizer::normalize($typed));
})->with([
    'hamza on alef' => ['أحمد', 'احمد'],
    'hamza below alef' => ['إسلام', 'اسلام'],
    'madda' => ['آمنة', 'امنه'],
    'taa marbuta' => ['مدرسة', 'مدرسه'],
    'alef maqsura' => ['مصطفى', 'مصطفي'],
    'diacritics' => ['مُحَمَّد', 'محمد'],
    'tatweel' => ['العـــراق', 'العراق'],
    'hamza on waw' => ['مؤتمر', 'موتمر'],
    'arabic-indic digits' => ['٢٠٢٤', '2024'],
    'latin accents and case' => ['Café POINT', 'cafe point'],
]);

it('keeps decimals but splits on punctuation', function () {
    expect(TextNormalizer::normalize('Point Iraq 5.0 — "Opening", day-1.'))->toBe('point iraq 5.0 opening day 1');
});

it('turns HTML into readable plain text', function () {
    expect(TextNormalizer::plainText('<p>Hello&nbsp;<b>world</b></p><p>مرحبا&amp;أهلا</p><style>p{}</style>'))
        ->toBe('Hello world مرحبا&أهلا');
});

it('extracts unique, limited search terms', function () {
    expect(TextNormalizer::terms('  أحمد  احمد Point   point a b c d e '))->toBe(['احمد', 'point', 'a', 'b', 'c', 'd']);
    expect(TextNormalizer::terms('  !!  '))->toBe([]);
});
