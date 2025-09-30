<?php

namespace App\Filament\Crm\Resources\Payrolls;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Crm\Resources\Payrolls\Pages\ManagePayrolls;
use App\Filament\Crm\Resources\PayrollResource\Pages;
use App\Models\Payroll;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'Personal';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('time_report_id')
                    ->relationship('timeReport', 'id')
                    ->hidden()
                    ->default(null),
                Select::make('previous_id')
                    ->relationship('previous', 'id')
                    ->hidden()
                    ->default(null),
                DatePicker::make('from')
                    ->required(),
                DatePicker::make('till')
                    ->required(),
                TextInput::make('minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('hourly_wage')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                TextInput::make('payment')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                TextInput::make('extra_payment')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                TextInput::make('prev_balance')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                TextInput::make('payout')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                TextInput::make('current_balance')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                KeyValue::make('meta')
                    ->required()
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
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('timeReport.id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('previous.id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('from')
                    ->date(getDateFormat())
                    ->sortable(),
                TextColumn::make('till')
                    ->date(getDateFormat())
                    ->sortable(),
                TextColumn::make('minutes')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => formatTime($state)),
                TextColumn::make('hourly_wage')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('extra_payment')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('prev_balance')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payout')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('current_balance')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label(__('User'))
                    ->relationship('user', 'name')
                    ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name)
                    ->visible(auth()->user()->can('admin_leave'))
                    ->searchable(['name', 'firstname', 'lastname']),
                Filter::make('between')
                    ->schema([
                        DatePicker::make('from')
                            ->default(today()->startOfMonth()),
                        DatePicker::make('till')
                            ->default(today()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->where('from', '>=', $date),
                            )
                            ->when(
                                $data['till'],
                                fn (Builder $query, $date): Builder => $query->where('from', '<=', $date),
                            );
                    }),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePayrolls::route('/'),
        ];
    }
}
