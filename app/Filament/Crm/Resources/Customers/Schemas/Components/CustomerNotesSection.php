<?php

namespace App\Filament\Crm\Resources\Customers\Schemas\Components;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;

class CustomerNotesSection
{
    public static function make(): Section
    {
        return Section::make(__('Notes'))
            ->collapsible()
            ->compact()
            ->schema([
                Repeater::make('notes')
                    ->relationship('notes', fn (Builder $query) => $query
                        ->where(fn (Builder $query) => $query
                            ->where(fn (Builder $query) => $query->whereNull('notable_type'))
                            ->orWhere('is_important', true)
                        ))
                    ->collapsible()
                    ->addActionLabel(__('Add note'))
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data, Get $get): array {
                        $data['user_id'] = auth()->id();

                        return $data;
                    })
                    ->columns(2)
                    ->schema([
                        Toggle::make('edit')
                            ->inline(false)
                            ->default(true),
                        Toggle::make('is_important')
                            ->inline(false)
                            ->default(true),
                        Textarea::make('content')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->visibleJs(<<<'JS'
                                $get('edit')
                            JS),
                        TextEntry::make('text')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->color(fn (Get $get) => $get('is_important') ? 'danger' : null)
                            ->state(fn (Get $get) => $get('content'))
                            ->hiddenJS(<<<'JS'
                                $get('edit')
                            JS),
                        TextEntry::make('created_at')
                            ->hiddenLabel()
                            ->date(getDateFormat()),
                        TextEntry::make('user.name')
                            ->hiddenLabel(),
                    ]),
                ]);
    }
}
