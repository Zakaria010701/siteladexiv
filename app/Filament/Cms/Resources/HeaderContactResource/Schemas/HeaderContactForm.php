<?php

namespace App\Filament\Cms\Resources\HeaderContactResource\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HeaderContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'lg' => 2,
            ])
            ->components([
                Section::make('Basic Information')
                    ->columns(3)
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('welcome_text')
                            ->required()
                            ->default('Welcome to our website')
                            ->maxLength(255),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        TextInput::make('position')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),
                    ]),

                Section::make('Contact Information')
                    ->columns(3)
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->placeholder('069 2108 9619')
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->placeholder('Kontakt-Formular')
                            ->maxLength(255),

                        Textarea::make('address')
                            ->label('Address')
                            ->placeholder('Roßmarkt 15, Frankfurt Friedrichstraße 57, Wiesbaden')
                            ->rows(3)
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),

                Section::make('Social Media Links')
                    ->columns(3)
                    ->schema([
                        TextInput::make('facebook_url')
                            ->label('Facebook URL')
                            ->placeholder('https://facebook.com/...')
                            ->url()
                            ->maxLength(255),

                        TextInput::make('instagram_url')
                            ->label('Instagram URL')
                            ->placeholder('https://instagram.com/...')
                            ->url()
                            ->maxLength(255),

                        TextInput::make('tiktok_url')
                            ->label('TikTok URL')
                            ->placeholder('https://tiktok.com/...')
                            ->url()
                            ->maxLength(255),
                    ]),

                Section::make('Language Flags')
                    ->columns(2)
                    ->schema([
                        TextInput::make('german_flag_icon')
                            ->label('German Flag Icon')
                            ->placeholder('SVG code or icon class')
                            ->maxLength(255),

                        TextInput::make('english_flag_icon')
                            ->label('English Flag Icon')
                            ->placeholder('SVG code or icon class')
                            ->maxLength(255),
                    ]),
            ]);
    }
}