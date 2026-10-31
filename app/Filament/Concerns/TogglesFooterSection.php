<?php

namespace App\Filament\Concerns;

use App\Enums\FooterSection;
use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

/**
 * Lets a list page show or hide its resource's logo section in the public site footer:
 * a hide/show header button plus a subheading with the current state.
 */
trait TogglesFooterSection
{
    // Per-request cache; Livewire doesn't persist protected properties between requests.
    protected ?bool $shownInFooter = null;

    abstract protected function footerSection(): FooterSection;

    public function getSubheading(): ?string
    {
        return $this->isShownInFooter()
            ? 'Shown in the site footer / ظاهر في حاشية الموقع'
            : 'Hidden from the site footer / مخفي من حاشية الموقع';
    }

    /**
     * Only the button matching the current state is visible, and Filament refuses to run
     * hidden actions, so a click from an outdated page never flips the state the wrong way.
     *
     * @return array<Action>
     */
    protected function footerVisibilityActions(): array
    {
        return [
            Action::make('hideFromFooter')
                ->label('Hide from footer / إخفاء من الحاشية')
                ->icon('heroicon-o-eye-slash')
                ->color('gray')
                ->visible(fn (): bool => $this->isShownInFooter())
                ->action(fn () => $this->setShownInFooter(false)),
            Action::make('showInFooter')
                ->label('Show in footer / إظهار في الحاشية')
                ->icon('heroicon-o-eye')
                ->color('success')
                ->hidden(fn (): bool => $this->isShownInFooter())
                ->action(fn () => $this->setShownInFooter(true)),
        ];
    }

    private function isShownInFooter(): bool
    {
        return $this->shownInFooter ??= $this->footerSection()->isVisible();
    }

    private function setShownInFooter(bool $visible): void
    {
        if (! SiteSetting::tableExists()) {
            Notification::make()
                ->danger()
                ->title('The settings table is missing: run the database migrations first')
                ->send();

            return;
        }

        $this->footerSection()->setVisible($visible);
        $this->shownInFooter = $visible;

        Notification::make()
            ->success()
            ->title($visible
                ? 'Shown in the site footer / تم الإظهار في حاشية الموقع'
                : 'Hidden from the site footer / تم الإخفاء من حاشية الموقع')
            ->send();
    }
}
