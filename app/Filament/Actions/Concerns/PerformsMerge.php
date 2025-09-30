<?php

namespace App\Filament\Actions\Concerns;

use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use App\Models\Customer;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

trait PerformsMerge
{
    public static function getDefaultName(): ?string
    {
        return 'merge';
    }

    private function performSetUp(): void
    {
        $this->label(__('Merge'));

        /** @phpstan-ignore-next-line */
        $this->modalHeading(function (Model $record): string {
            if ($record instanceof Customer) {
                return __('Merge :customer', ['customer' => $record->full_name]);
            }

            if (isset($record->customer) && $record->customer instanceof Customer) {
                return __('Merge :customer', ['customer' => $record->customer->full_name]);
            }

            return __('Merge');
        });

        $this->icon('heroicon-o-arrows-right-left');

        /** @phpstan-ignore-next-line */
        $this->fillForm(function (Model $record): array {
            if ($record instanceof Customer) {
                return $this->getData($record);
            }

            if (isset($record->customer) && $record->customer instanceof Customer) {
                return $this->getData($record->customer);
            }

            return [];
        });

        /** @phpstan-ignore-next-line */
        $this->steps($this->getSteps());

        $this->action(function (array $data, Model $record): void {
            if ($record instanceof Customer) {
                $this->merge($data, $record);

                return;
            }

            if (isset($record->customer) && $record->customer instanceof Customer) {
                $this->merge($data, $record->customer);
            }
        });
    }


}
