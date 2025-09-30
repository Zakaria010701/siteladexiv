<?php

namespace App\Filament\Crm\Resources\TimeReports;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Crm\Resources\TimeReports\Pages\ListTimeReports;
use App\Filament\Crm\Resources\TimeReports\Pages\TimeClock;
use App\Filament\Crm\Resources\TimeReports\Pages\OverviewTimeReport;
use App\Filament\Crm\Resources\TimeReports\Pages\CreateTimeReport;
use App\Filament\Crm\Resources\TimeReports\Pages\EditTimeReport;
use App\Enums\TimeRecords\LeaveType;
use App\Filament\Crm\Resources\TimeReportResource\Pages;
use App\Models\TimeReport;
use Filament\Forms;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class TimeReportResource extends Resource
{
    protected static ?string $model = TimeReport::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'Personal';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TimePicker::make('time_in')
                    ->dehydrateStateUsing(fn ($state, Get $get) => isset($state) ? Carbon::parse($get('date'))->setTimeFromTimeString($state)->format('Y-m-d H:i:s') : null),
                TimePicker::make('time_out')
                    ->dehydrateStateUsing(fn ($state, Get $get) => isset($state) ? Carbon::parse($get('date'))->setTimeFromTimeString($state)->format('Y-m-d H:i:s') : null),
                TimePicker::make('real_time_in')
                    ->visible(auth()->user()->can('admin_time::report'))
                    ->dehydrateStateUsing(fn ($state, Get $get) => isset($state) ? Carbon::parse($get('date'))->setTimeFromTimeString($state)->format('Y-m-d H:i:s') : null),
                TimePicker::make('real_time_out')
                    ->visible(auth()->user()->can('admin_time::report'))
                    ->dehydrateStateUsing(fn ($state, Get $get) => isset($state) ? Carbon::parse($get('date'))->setTimeFromTimeString($state)->format('Y-m-d H:i:s') : null),
                TextInput::make('total_minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('break_minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('actual_minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('manual_minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('overtime_minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('uncapped_overtime_minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_overtime_capped')
                    ->visible(fn () => auth()->user()->can('admin_time::report'))
                    ->required(),
                Select::make('leave_type')
                    ->options(LeaveType::class)
                    ->default(null),
                Textarea::make('note')
                    ->columnSpanFull(),
                KeyValue::make('meta')
                    ->visible(fn () => auth()->user()->can('admin_time::report'))
                    ->columnSpanFull(),
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
                TextColumn::make('user.name')
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('target_minutes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('work_time_start')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('work_time_end')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('work_time_minutes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('time_in')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('time_in_status')
                    ->badge(),
                TextColumn::make('time_out')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('time_out_status')
                    ->searchable(),
                TextColumn::make('total_minutes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('real_time_in')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('real_time_out')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('real_total_minutes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('break_minutes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('actual_minutes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('manual_minutes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('overtime_minutes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('uncapped_overtime_minutes')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_overtime_capped')
                    ->boolean(),
                TextColumn::make('leave_type')
                    ->searchable(),
                TextColumn::make('edited_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('editedBy.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('controlled_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('controlledBy.name')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
            'table' => ListTimeReports::route('/table'),
            'clock' => TimeClock::route('/clock'),
            'index' => OverviewTimeReport::route('/'),
            'create' => CreateTimeReport::route('/create'),
            'edit' => EditTimeReport::route('/{record}/edit'),
        ];
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->parentItem(static::getNavigationParentItem())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->isActiveWhen(fn () => request()->routeIs(static::getRouteBaseName().'.*') && ! request()->routeIs(static::getRouteBaseName().'.clock'))
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->badgeTooltip(static::getNavigationBadgeTooltip())
                ->sort(static::getNavigationSort())
                ->url(static::getNavigationUrl()),
            NavigationItem::make(__('Clock'))
                ->group(static::getNavigationGroup())
                ->parentItem(static::getNavigationParentItem())
                ->icon('heroicon-o-clock')
                ->isActiveWhen(fn () => request()->routeIs(static::getRouteBaseName().'.clock'))
                ->sort(-100)
                ->url(static::getUrl('clock')),
        ];
    }
}
