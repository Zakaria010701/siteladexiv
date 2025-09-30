<?php

namespace App\Filament\Crm\Resources\WorkTimes;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Crm\Resources\WorkTimes\Pages\ManageWorkTimes;
use App\Enums\TimeStep;
use App\Enums\WorkTimes\WorkTimeType;
use App\Filament\Crm\Resources\WorkTimeResource\Pages;
use App\Models\Room;
use App\Models\WorkTime;
use App\Models\WorkTimeGroup;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class WorkTimeResource extends Resource
{
    protected static ?string $model = WorkTime::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'Personal';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        DatePicker::make('date')
                            ->required(),
                        TimePicker::make('start')
                            ->required(),
                        TimePicker::make('end')
                            ->required(),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required(),
                        Select::make('room_id')
                            ->relationship('room', 'name')
                            ->required(),
                        Select::make('type')
                            ->options(WorkTimeType::class)
                            ->required(),
                        Select::make('repeat_step')
                            ->live(onBlur: true)
                            ->hidden(fn (string $operation) => ! Str::startsWith($operation, 'create'))
                            ->options(TimeStep::class)
                            ->required(),
                        TextInput::make('repeat_every')
                            ->hidden(fn (Get $get, string $operation) => $get('repeat_step') == TimeStep::None->value || ! Str::startsWith($operation, 'create'))
                            ->required(),
                        DatePicker::make('repeat_till')
                            ->hidden(fn (Get $get, string $operation) => $get('repeat_step') == TimeStep::None->value || ! Str::startsWith($operation, 'create'))
                            ->required(),
                    ]),
            ]);
    }

    public static function createUsing(array $data): WorkTime
    {
        $start = Carbon::parse($data['date'])->setTimeFromTimeString($data['start']);
        $end = Carbon::parse($data['date'])->setTimeFromTimeString($data['end']);
        $step = TimeStep::from($data['repeat_step']);
        $branch = Room::find($data['room_id'])->branch_id;

        if ($step == TimeStep::None) {
            $data['start'] = $start->format('Y-m-d H:i:s');
            $data['end'] = $end->format('Y-m-d H:i:s');
            $data['branch_id'] = $branch;

            return WorkTime::create($data);
        }

        $startTime = $start->format('H:i:s');
        $endTime = $end->format('H:i:s');

        $group = WorkTimeGroup::create([
            'user_id' => $data['user_id'],
            'room_id' => $data['room_id'],
            'branch_id' => $branch,
            'type' => $data['type'],
            'start' => $startTime,
            'end' => $endTime,
            'repeat_step' => $data['repeat_step'],
            'repeat_from' => $start->format('Y-m-d'),
            'repeat_till' => $data['repeat_till'],
            'repeat_every' => $data['repeat_every'],
        ]);
        $period = $step->getInterval($data['repeat_every'])->toPeriod($start->format('Y-m-d'), $data['repeat_till']);

        foreach ($period as $date) {
            $startT = $date->copy()->setTimeFromTimeString($startTime);
            $endT = $date->copy()->setTimeFromTimeString($endTime);
            $group->workTimes()->create([
                'user_id' => $data['user_id'],
                'room_id' => $data['room_id'],
                'branch_id' => $branch,
                'type' => $data['type'],
                'start' => $startT,
                'end' => $endT,
            ]);
        }

        return $group->workTimes()->orderBy('start')->first();
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
                TextColumn::make('workTimeGroup.id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('branch.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('room.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('start')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('end')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
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
            'index' => ManageWorkTimes::route('/'),
        ];
    }
}
