<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class ContactFormBlock
{
    public static function make(): Block
    {
        return Block::make('contact_form')
            ->icon('heroicon-o-envelope')
            ->schema([
                TextInput::make('title')
                    ->label('Form Title')
                    ->default('Kontaktieren Sie uns')
                    ->required(),
                Textarea::make('description')
                    ->label('Form Description')
                    ->default('Senden Sie uns eine Nachricht und wir melden uns schnellstmöglich bei Ihnen.')
                    ->rows(3),
                TextInput::make('email_to')
                    ->label('Email Empfänger')
                    ->default('info@example.com')
                    ->required()
                    ->email(),
                Select::make('layout')
                    ->label('Layout')
                    ->options([
                        'single' => 'Single Column',
                        'two_columns' => 'Two Columns',
                    ])
                    ->default('single')
                    ->required(),
                TextInput::make('submit_button_text')
                    ->label('Submit Button Text')
                    ->default('Nachricht senden')
                    ->required(),
            ]);
    }
}