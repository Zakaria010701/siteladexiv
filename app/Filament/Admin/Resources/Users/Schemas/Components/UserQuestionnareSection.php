<?php

namespace App\Filament\Admin\Resources\Users\Schemas\Components;

use App\Enums\Gender;
use App\Enums\User\MaritalStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;

class UserQuestionnareSection {

    public static function make(): Section
    {
        return Section::make(__('Personal questionnaire'))
            ->columns(3)
            ->relationship('userDetails')
            ->schema([
                Select::make('gender')
                    ->options(Gender::class),
                Select::make('marital_status')
                    ->options(MaritalStatus::class),
                Toggle::make('severely_disabled')
                    ->inline(false),
                TextInput::make('place_of_birth'),
                TextInput::make('country'),
                TextInput::make('nationality'),
            ]);
    }

}
