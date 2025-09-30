<?php

namespace App\Filament\Crm\Resources\Leaves;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ExportAction;
use App\Filament\Crm\Resources\Leaves\Pages\ListLeaves;
use App\Filament\Crm\Resources\Leaves\Pages\CreateLeave;
use App\Filament\Crm\Resources\Leaves\Pages\EditLeave;
use App\Actions\TimeReport\ApproveLeave;
use App\Actions\TimeReport\DenyLeave;
use App\Actions\TimeReport\FindTotalLeaveDaysBetween;
use App\Enums\TimeRecords\LeaveType;
use App\Filament\Crm\Resources\LeaveResource\Pages;
use App\Filament\Exports\LeaveExporter;
use App\Models\Leave;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'Personal';

    public static function getModelLabel(): string
    {
        return __('Leave');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Leaves');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(__('User'))
                    ->relationship('user', 'name')
                    ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name)
                    ->searchable(['name', 'firstname', 'lastname'])
                    ->preload()
                    ->default(Filament::auth()->id())
                    ->disabled(Filament::auth()->user()->cannot('admin_leave'))
                    ->dehydrated()
                    ->required(),
                Select::make('leave_type')
                    ->options(LeaveType::class)
                    ->required(),
                DatePicker::make('from')
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $from = $get('from');
                        $till = $get('till');
                        $user = User::find($get('user_id'));

                        if (empty($from) || empty($till) || empty($user)) {
                            return;
                        }

                        $total = FindTotalLeaveDaysBetween::make($user, Carbon::parse($from), Carbon::parse($till))->execute();
                        $set('total_leave_days', $total);
                    })
                    ->required(),
                DatePicker::make('till')
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $from = $get('from');
                        $till = $get('till');
                        $user = User::find($get('user_id'));

                        if (empty($from) || empty($till) || empty($user)) {
                            return;
                        }

                        $total = FindTotalLeaveDaysBetween::make($user, Carbon::parse($from), Carbon::parse($till))->execute();
                        $set('total_leave_days', $total);
                    })
                    ->required(),
                TextInput::make('total_leave_days')
                    ->integer()
                    ->readOnly()
                    ->required(),
                Textarea::make('user_note')
                    ->columnSpanFull(),
                Textarea::make('admin_note')
                    ->columnSpanFull()
                    ->visible(Filament::auth()->user()->can('admin_leave')),
                KeyValue::make('meta')
                    ->columnSpanFull()
                    ->visible(Filament::auth()->user()->can('admin_leave')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('leave_type')
                    ->badge()
                    ->searchable(),
                TextColumn::make('from')
                    ->date(getDateFormat())
                    ->sortable(),
                TextColumn::make('till')
                    ->date(getDateFormat())
                    ->sortable(),
                TextColumn::make('total_leave_days')
                    ->sortable(),
                TextColumn::make('processedBy.name')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('approved_at')
                    ->label(__('Approved'))
                    ->boolean()
                    ->default(false)
                    ->tooltip(fn (mixed $state) => $state ? formatDateTime($state) : null)
                    ->sortable(),
                IconColumn::make('denied_at')
                    ->label(__('Denied'))
                    ->boolean()
                    ->default(false)
                    ->tooltip(fn (mixed $state) => $state ? formatDateTime($state) : null)
                    ->sortable(),
            ])
            ->defaultSort('from', 'desc')
            ->filters([
                SelectFilter::make('user_id')
                    ->label(__('User'))
                    ->relationship('user', 'name')
                    ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name)
                    ->visible(auth()->user()->can('admin_leave'))
                    ->searchable(['name', 'firstname', 'lastname']),
                TernaryFilter::make('approved_at')
                    ->label(__('Approved'))
                    ->nullable(),
                TernaryFilter::make('denied_at')
                    ->label(__('Denied'))
                    ->nullable(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                ActionGroup::make([
                    Action::make('approve')
                        ->requiresConfirmation()
                        ->color('success')
                        ->icon('heroicon-m-check-circle')
                        ->visible(auth()->user()->can('admin_leave'))
                        ->action(function (Leave $record) {
                            ApproveLeave::make($record, auth()->user())->execute();

                            Notification::make()
                                ->success()
                                ->title(__('status.result.success'))
                                ->send();
                        }),
                    Action::make('deny')
                        ->requiresConfirmation()
                        ->color('warning')
                        ->icon('heroicon-m-x-circle')
                        ->visible(auth()->user()->can('admin_leave'))
                        ->action(function (Leave $record) {
                            DenyLeave::make($record, auth()->user())->execute();

                            Notification::make()
                                ->success()
                                ->title(__('status.result.success'))
                                ->send();
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->requiresConfirmation()
                        ->color('success')
                        ->icon('heroicon-m-check-circle')
                        ->visible(auth()->user()->can('admin_leave'))
                        ->action(function (Collection $selectedRecords) {
                            $selectedRecords->each(fn (Leave $record) => ApproveLeave::make($record, auth()->user())->execute());

                            Notification::make()
                                ->success()
                                ->title(__('status.result.success'))
                                ->send();
                        }),
                    BulkAction::make('deny')
                        ->requiresConfirmation()
                        ->color('warning')
                        ->icon('heroicon-m-x-circle')
                        ->visible(auth()->user()->can('admin_leave'))
                        ->action(function (Collection $selectedRecords) {
                            $selectedRecords->each(fn (Leave $record) => DenyLeave::make($record, auth()->user())->execute());

                            Notification::make()
                                ->success()
                                ->title(__('status.result.success'))
                                ->send();
                        }),
                    ExportBulkAction::make()
                        ->exporter(LeaveExporter::class),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(LeaveExporter::class),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeaves::route('/'),
            'create' => CreateLeave::route('/create'),
            'edit' => EditLeave::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
