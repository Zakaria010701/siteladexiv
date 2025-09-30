<?php

namespace App\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Support\Facades\FilamentView;

use function Filament\Support\is_app_url;

trait HasSaveAndCloseAction
{
    private function getSaveAndCloseFormAction(): Action
    {
        return Action::make('saveAndClose')
            ->action('saveAndClose');
    }

    public function saveAndClose(): void
    {
        $this->save();
        $redirectUrl = $this->previousUrl ?? static::getResource()::getUrl();
        $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode() && is_app_url($redirectUrl));
    }
}
