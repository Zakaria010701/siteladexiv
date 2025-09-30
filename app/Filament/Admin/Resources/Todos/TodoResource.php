<?php

namespace App\Filament\Admin\Resources\Todos;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Admin\Resources\Todos\RelationManagers\TodoItemRelationManager;
use App\Filament\Admin\Resources\Todos\Pages\ListTodos;
use App\Filament\Admin\Resources\Todos\Pages\CreateTodo;
use App\Filament\Admin\Resources\Todos\Pages\EditTodo;
use App\Enums\Todos\TodoPriority;
use App\Enums\Todos\TodoStatus;
use App\Filament\Admin\Resources\TodoResource\Pages;
use App\Filament\Admin\Resources\TodoResource\RelationManagers;
use App\Models\Todo;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TodoResource extends Resource
{
    protected static ?string $model = Todo::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-check';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('priority')
                ->required()
                ->default(TodoPriority::Low->value)
                ->options(TodoPriority::class),
            Select::make('status')
                ->default(TodoStatus::NotDone->value)
                ->required()
                ->options(TodoStatus::class),
            Select::make('users')
                ->relationship('users', 'name')
                ->default([auth()->id()])
                ->multiple()
                ->searchable()
                ->preload()
                ->required(),
            DatePicker::make('due_date')
                ->displayFormat('Y-m-d')
                ->placeholder('YYYY-MM-DD')
                ->default(Carbon::now()->toDateString()),
            MarkdownEditor::make('description')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('priority')
                    ->badge()
                    ->sortable(),
                TextColumn::make('progress')
                    ->formatStateUsing(fn ($state) => sprintf("%s %%", $state)),
                TextColumn::make('due_date')
                    ->date(getDateFormat()),
                TextColumn::make('description')
                    ->limit(50)
                    ->markdown()
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->sortable(),
                TextColumn::make('users.name')
                    ->searchable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(TodoStatus::class),
                SelectFilter::make('priority')
                    ->options(TodoPriority::class),
                SelectFilter::make('users')
                    ->multiple()
                    ->default([auth()->user()->id])
                    ->relationship('users', 'name')
            ]);

    }

    public static function getRelations(): array
    {
        return [
            TodoItemRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTodos::route('/'),
            'create' => CreateTodo::route('/create'),
            'edit' => EditTodo::route('/{record}/edit'),
            'index_duplicate' => ListTodos::route('/duplicate'),
        ];
    }
}
