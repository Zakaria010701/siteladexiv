<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use App\Filament\Admin\Resources\Users\UserResource;
use App\Filament\Tables\Columns\CreatedAtColumn;
use App\Filament\Tables\Columns\DeletedAtColumn;
use App\Filament\Tables\Columns\IdColumn;
use App\Filament\Tables\Columns\UpdatedAtColumn;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UserTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                CreatedAtColumn::make(),
                UpdatedAtColumn::make(),
                DeletedAtColumn::make(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('firstname')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lastname')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone_number')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_provider')
                    ->label(__('user.is_provider'))
                    ->boolean()
                    ->sortable(),
                IconColumn::make('show_in_frontend')
                    ->label(__('user.show_in_frontend'))
                    ->boolean()
                    ->sortable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                TernaryFilter::make('is_provider')
                    ->label(__('user.is_provider')),
                TernaryFilter::make('show_in_frontend')
                    ->label(__('user.show_in_frontend')),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('details')
                        ->icon('heroicon-m-arrow-top-right-on-square')
                        ->url(fn ($record) => UserResource::getUrl('user-details', ['record' => $record])),
                    Action::make('provider')
                        ->icon('heroicon-m-arrow-top-right-on-square')
                        ->url(fn ($record) => UserResource::getUrl('provider', ['record' => $record])),
                    Action::make('calendar')
                        ->icon('heroicon-m-calendar')
                        ->url(fn ($record) => UserResource::getUrl('calendar', ['record' => $record])),
                    ActionGroup::make([
                        DeleteAction::make(),
                        ForceDeleteAction::make(),
                        RestoreAction::make(),
                    ])->dropdown(false),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
