<?php

namespace App\Filament\Actions\Table;

use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Get;
use App\Models\Customer;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EditPassword extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'edit-password';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Edit password'));

        $this->modalHeading(fn (): string => __('Edit :label password', ['label' => $this->getRecordTitle()]));

        $this->icon('heroicon-o-lock-closed');

        $this->form([
            TextInput::make('password')
                ->label(__('filament-panels::pages/auth/edit-profile.form.password.label'))
                ->password()
                ->revealable()
                ->rule(Password::default())
                ->autocomplete('new-password')
                ->dehydrated(fn ($state): bool => filled($state))
                ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                ->live(debounce: 500)
                ->same('passwordConfirmation'),
            TextInput::make('passwordConfirmation')
                ->label(__('filament-panels::pages/auth/edit-profile.form.password_confirmation.label'))
                ->password()
                ->revealable()
                ->required()
                ->visible(fn (Get $get): bool => filled($get('password')))
                ->dehydrated(false),
        ]);

        $this->action(function (Customer $customer, array $data) {
            $customer->password = $data['password'];
            $customer->save();

            Notification::make()
                ->title(__('Password updated'))
                ->success()
                ->send();
        });
    }
}
