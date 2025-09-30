<?php

namespace App\Filament\Admin\Resources\Availabilities;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use App\Forms\Components\TableRepeater\Header;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use App\Filament\Admin\Resources\Availabilities\RelationManagers\AvailabilityExceptionsRelationManager;
use App\Filament\Admin\Resources\Availabilities\RelationManagers\AvailabilityAbsencesRelationManager;
use App\Filament\Admin\Resources\Availabilities\Pages\ListAvailabilities;
use App\Filament\Admin\Resources\Availabilities\Pages\CreateAvailability;
use App\Filament\Admin\Resources\Availabilities\Pages\EditAvailability;
use App\Enums\TimeStep;
use App\Enums\Weekday;
use App\Filament\Admin\Resources\Availabilities\Schemas\AvailabilityForm;
use App\Filament\Admin\Resources\Availabilities\Tables\AvailabilitiesTable;
use App\Filament\Admin\Resources\AvailabilityResource\Pages;
use App\Filament\Admin\Resources\AvailabilityResource\RelationManagers;
use App\Forms\Components\TableRepeater;
use App\Models\Availability;
use App\Models\AvailabilityType;
use App\Models\Branch;
use App\Models\Invoice;
use App\Models\SystemResource;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\MorphToSelect;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AvailabilityResource extends Resource
{
    protected static ?string $model = Availability::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-s-calendar-date-range';

    public static function getModelLabel(): string
    {
        return __('availability.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('availability.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return AvailabilityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AvailabilitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AvailabilityExceptionsRelationManager::class,
            AvailabilityAbsencesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAvailabilities::route('/'),
            'create' => CreateAvailability::route('/create'),
            'edit' => EditAvailability::route('/{record}/edit'),
        ];
    }
}
