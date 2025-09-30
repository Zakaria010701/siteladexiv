<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Filament\Admin\Resources\Users\Schemas\Components\UserAddressFieldset;
use App\Filament\Admin\Resources\Users\Schemas\Components\UserEmailFieldset;
use App\Filament\Admin\Resources\Users\Schemas\Components\UserNameFieldset;
use App\Filament\Admin\Resources\Users\Schemas\Components\UserPasswordInput;
use App\Filament\Admin\Resources\Users\Schemas\Components\UserPhoneFieldset;
use App\Filament\Admin\Resources\Users\Schemas\Components\UserRolesSelect;
use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->compact()
                    ->schema(self::components()),
            ]);
    }

    public static function step(): Step
    {
        return Step::make(__('User'))
            ->icon(Heroicon::User)
            ->columns(2)
            ->schema(self::components());
    }

    public static function components(): array
    {
        return [
            UserNameFieldset::make(),
            DatePicker::make('birthday'),
            Select::make('user_work_type_id')
                ->required()
                ->relationship('userWorkType', 'name'),
            UserPasswordInput::make(),
            UserRolesSelect::make(),
            UserEmailFieldset::make(),
            UserPhoneFieldset::make(),
            UserAddressFieldset::make(),
        ];
    }
}
