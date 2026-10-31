<?php

namespace App\Enums;

use App\Models\SiteSetting;

/**
 * Logo sections of the public site footer that admins can show or hide.
 */
enum FooterSection: string
{
    case Organizers = 'organizers';
    case Partners = 'partners';

    public function isVisible(): bool
    {
        return (bool) SiteSetting::get($this->settingKey(), true);
    }

    public function setVisible(bool $visible): void
    {
        SiteSetting::set($this->settingKey(), $visible);
    }

    private function settingKey(): string
    {
        return "footer.show_{$this->value}";
    }
}
