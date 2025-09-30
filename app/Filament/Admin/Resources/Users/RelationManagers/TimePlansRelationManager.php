<?php

namespace App\Filament\Admin\Resources\Users\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Models\User;
use App\Enums\TimeRecords\TimeConstraint;
use App\Enums\User\WageType;
use App\Models\TimePlan;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class TimePlansRelationManager extends RelationManager
{
    protected static string $relationship = 'timePlans';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('start_date')
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, RelationManager $livewire) {
                        return $rule->where('user_id', $livewire->getOwnerRecord()->id);
                    })
                    ->required(),
                DatePicker::make('end_date'),
                Select::make('time_constraint')
                    ->label(__('settings.work_types.label.time_constraint'))
                    ->helperText(__('settings.work_types.helper.time_constraint'))
                    ->required()
                    ->options(TimeConstraint::class),
                TextInput::make('wage')
                    ->columnStart(1)
                    ->numeric()
                    ->default(0)
                    ->required(),
                Select::make('wage_type')
                    ->options(WageType::class)
                    ->default(WageType::Monthly)
                    ->required(),
                TextInput::make('yearly_vacation_days')
                    ->integer()
                    ->default(0)
                    ->columnStart(1)
                    ->required(),
                TextInput::make('start_vacation_days')
                    ->integer()
                    ->default(0)
                    ->required(),
                Fieldset::make(__('Target Hours'))
                    ->columns(3)
                    ->schema([
                        TextInput::make('monday_hours')
                            ->integer()
                            ->default(0)
                            ->required(),
                        TextInput::make('tuesday_hours')
                            ->integer()
                            ->default(0)
                            ->required(),
                        TextInput::make('wednesday_hours')
                            ->integer()
                            ->default(0)
                            ->required(),
                        TextInput::make('thursday_hours')
                            ->integer()
                            ->default(0)
                            ->required(),
                        TextInput::make('friday_hours')
                            ->integer()
                            ->default(0)
                            ->required(),
                        TextInput::make('saturday_hours')
                            ->integer()
                            ->default(0)
                            ->required(),
                        TextInput::make('sunday_hours')
                            ->integer()
                            ->default(0)
                            ->required(),
                    ]),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('start_date')
            ->columns([
                TextColumn::make('start_date')
                    ->date(getDateFormat()),
                TextColumn::make('end_date')
                    ->date(getDateFormat()),
                TextColumn::make('time_constraint')
                    ->label(__('settings.work_types.label.time_constraint'))
                    ->searchable()
                    ->badge(),
                TextColumn::make('monday_hours'),
                TextColumn::make('tuesday_hours'),
                TextColumn::make('wednesday_hours'),
                TextColumn::make('thursday_hours'),
                TextColumn::make('friday_hours'),
                TextColumn::make('saturday_hours'),
                TextColumn::make('sunday_hours'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->fillForm(function (RelationManager $livewire) {
                        /** @var User */
                        $owner = $livewire->getOwnerRecord();

                        $start = $owner->timePlans()
                            ->whereNotNull('end_date')
                            ->orderByDesc('end_date')
                            ->first()?->end_date?->addDay() ?? today()->startOfMonth();

                        return [
                            'start_date' => $start->format('Y-m-d'),
                            'time_constraint' => $owner->userWorkType->time_constraint->value,
                            'monday_hours' => 0,
                            'tuesday_hours' => 0,
                            'wednesday_hours' => 0,
                            'thursday_hours' => 0,
                            'friday_hours' => 0,
                            'saturday_hours' => 0,
                            'sunday_hours' => 0,
                        ];
                    })
                    ->after(function (TimePlan $record) {
                        $timePlan = $record->user->timePlans()
                            ->whereNull('end_date')
                            ->where('start_date', '<', $record->start_date)
                            ->first();

                        if (is_null($timePlan)) {
                            return;
                        }

                        $timePlan->end_date = $record->start_date->subDay();
                        $timePlan->save();
                    }),
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
}
