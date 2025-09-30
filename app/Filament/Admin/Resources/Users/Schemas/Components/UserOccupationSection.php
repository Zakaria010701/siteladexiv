<?php

namespace App\Filament\Admin\Resources\Users\Schemas\Components;

use App\Enums\User\Occupation;
use App\Enums\User\WageType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class UserOccupationSection {

    public static function make(): Section
    {
        return Section::make(__('Occupation'))
            ->columns(3)
            ->relationship('userDetails')
            ->schema([
                DatePicker::make('the_beginning_of_employment'),
                Select::make('occupation')
                    ->options(Occupation::class),
                TextInput::make('weekly_hours')
                    ->integer(),
                TextInput::make('probationary_period')
                    ->integer(),
                Toggle::make('no_vacation_during_probationary_period')
                    ->inline(false),
                Toggle::make('other_jobs')
                    ->columnStart(1)
                    ->live()
                    ->inline(false),
                TextInput::make('other_occupation_employer')
                    ->hidden(fn (Get $get) => ! $get('other_jobs')),
                TextInput::make('other_occupation_weekly_hours')
                    ->hidden(fn (Get $get) => ! $get('other_jobs'))
                    ->integer(),
                TextInput::make('wage')
                    ->columnStart(1)
                    ->numeric(),
                Select::make('wage_type')
                    ->options(WageType::class),
                Toggle::make('limited')
                    ->inline(false)
                    ->label(__('Limited employment')),
                TextInput::make('second_wage')
                    ->numeric(),
                Select::make('second_wage_type')
                    ->options(WageType::class),
                Textarea::make('note'),
            ]);
    }

}
