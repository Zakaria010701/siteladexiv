<?php

namespace App\Filament\Admin\Concerns;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Select;
use App\Forms\Components\TableRepeater\Header;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\CreateAction;
use Filament\Support\Enums\Width;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Enums\TimeStep;
use App\Enums\Weekday;
use App\Filament\Admin\Resources\Availabilities\AvailabilityResource;
use App\Filament\Admin\Resources\Availabilities\Schemas\AvailabilityForm;
use App\Filament\Admin\Resources\Availabilities\Schemas\Components\AvailabilityAdvancedSettingsFieldset;
use App\Filament\Admin\Resources\Availabilities\Schemas\Components\AvailabilityDateFieldset;
use App\Filament\Admin\Resources\Availabilities\Schemas\Components\AvailabilityPlanableSelect;
use App\Filament\Admin\Resources\Availabilities\Schemas\Components\AvailabilityShiftsRepeater;
use App\Filament\Admin\Resources\Availabilities\Schemas\Components\AvailabilityTypeSelect;
use App\Forms\Components\TableRepeater;
use App\Models\Availability;
use App\Models\AvailabilityType;
use Filament\Forms;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;

trait ManagesAvailabilitiesRelation
{
    public function form(Schema $schema): Schema
    {
        return AvailabilityForm::configureModal($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('start_date')
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('availabilityType.name')
                    ->badge()
                    ->color(fn (Availability $record) => Color::generateV3Palette($record->availabilityType->color)),
                TextColumn::make('start_date')
                    ->sortable()
                    ->date(getDateFormat()),
                TextColumn::make('end_date')
                    ->sortable()
                    ->date(getDateFormat()),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalWidth(Width::ScreenExtraLarge)
                    ->mutateDataUsing(function (array $data): array {
                        $data['title'] = $this->getOwnerRecord()->name;
                        $type = AvailabilityType::findOrFail($data['availability_type']);

                        $data['color'] = $type->color;
                        $data['is_hidden'] = $type->is_hidden;
                        $data['is_all_day'] = $type->is_all_day;
                        $data['is_background'] = $type->is_background;
                        $data['is_background_inverted'] = $type->is_background_inverted;

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->icon('heroicon-m-eye')
                    ->url(fn (Availability $record) => AvailabilityResource::getUrl('edit', ['record' => $record])),
                EditAction::make()
                    ->iconButton(),
                DeleteAction::make()
                    ->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
