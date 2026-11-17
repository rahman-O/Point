<?php

namespace App\Enums;

/**
 * Sections of the public agenda. Every agenda item belongs to exactly one, and the agenda
 * page lists the sections in the order the cases are declared here.
 */
enum SessionCategory: string
{
    case Sessions = 'sessions';
    case Workshops = 'workshops';
    case Meetings = 'meetings';
    case Forums = 'forums';

    public const DEFAULT = self::Sessions;

    public function label(): string
    {
        return "{$this->labelEn()} / {$this->labelAr()}";
    }

    public function labelEn(): string
    {
        return match ($this) {
            self::Sessions => 'Sessions',
            self::Workshops => 'Workshops & Trainings',
            self::Meetings => 'National & Regional Meetings',
            self::Forums => 'National Forums',
        };
    }

    public function labelAr(): string
    {
        return match ($this) {
            self::Sessions => 'الجلسات',
            self::Workshops => 'الورش والتدريبات',
            self::Meetings => 'الاجتماعات الوطنية والإقليمية',
            self::Forums => 'الملتقيات الوطنية',
        };
    }

    public function emptyMessageEn(): string
    {
        return match ($this) {
            self::Sessions => 'No sessions are scheduled for this day.',
            self::Workshops => 'No workshops or trainings are scheduled for this day.',
            self::Meetings => 'No national or regional meetings are scheduled for this day.',
            self::Forums => 'No national forums are scheduled for this day.',
        };
    }

    public function emptyMessageAr(): string
    {
        return match ($this) {
            self::Sessions => 'لا توجد جلسات مجدولة في هذا اليوم.',
            self::Workshops => 'لا توجد ورش أو تدريبات مجدولة في هذا اليوم.',
            self::Meetings => 'لا توجد اجتماعات وطنية أو إقليمية مجدولة في هذا اليوم.',
            self::Forums => 'لا توجد ملتقيات وطنية مجدولة في هذا اليوم.',
        };
    }

    /**
     * Sections in display order, as the public agenda API sends them.
     *
     * @return list<array{key: string, label_en: string, label_ar: string, empty_en: string, empty_ar: string}>
     */
    public static function forApi(): array
    {
        return array_map(fn (self $c) => [
            'key' => $c->value,
            'label_en' => $c->labelEn(),
            'label_ar' => $c->labelAr(),
            'empty_en' => $c->emptyMessageEn(),
            'empty_ar' => $c->emptyMessageAr(),
        ], self::cases());
    }

    /**
     * Best guess for an item created before categories existed, from words in its title.
     */
    public static function guessFromTitle(?string ...$titles): self
    {
        $title = mb_strtolower(implode(' ', array_filter($titles)));

        foreach ([
            self::Workshops->value => ['ورشة', 'ورش ', 'تدريب', 'workshop', 'training'],
            self::Forums->value => ['ملتقى', 'الملتقى', 'ملتقيات', 'forum'],
            self::Meetings->value => ['اجتماع', 'الاجتماع', 'اجتماعات', 'meeting'],
        ] as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($title, $keyword)) {
                    return self::from($category);
                }
            }
        }

        return self::DEFAULT;
    }

    /**
     * @return array<string, string> value => bilingual label, for admin selects
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
