<?php

namespace App\Filament\Crm\Resources\Customers\RelationManagers;

use App\Filament\Crm\Resources\Customers\RelationManagers\VerificationsRelationManager;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use App\Enums\Verifications\VerificationStatus;
use App\Filament\Actions\Table\NeedsVerificationAction;
use App\Filament\Actions\Table\VerifyAction;
use Filament\Forms;
use Filament\Resources\Components\Tab;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VerificationsRelationManager extends RelationManager
{
    protected static string $relationship = 'verifications';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('status')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status')
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('status'),
                TextColumn::make('created_at')
                    ->dateTime(getDateTimeFormat())
                    ->sortable(),
                TextColumn::make('user.name'),
                TextColumn::make('note'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                VerifyAction::make()
                    ->visible(fn (VerificationsRelationManager $livewire) => $livewire->getOwnerRecord()->isNotVerified())
                    ->using(function (array $data, VerificationsRelationManager $livewire) {
                        $livewire->getOwnerRecord()->verifications()->create([
                            'user_id' => auth()->user()->id,
                            'status' => VerificationStatus::Pass,
                            'note' => $data['note'] ?? null,
                        ]);
                    }),
                NeedsVerificationAction::make()
                    ->using(function (array $data, VerificationsRelationManager $livewire) {
                        $livewire->getOwnerRecord()->verifications()->create([
                            'user_id' => auth()->user()->id,
                            'status' => VerificationStatus::Failure,
                            'note' => $data['note'] ?? null,
                        ]);
                    }),
            ]);
    }
}
