<?php

namespace App\Filament\Crm\Resources\Appointments;

use App\Filament\Crm\Resources\Customers\CustomerResource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Schemas\Schema;
use App\Filament\Crm\Resources\Appointments\RelationManagers\AppointmentsRelationManager;
use App\Filament\Crm\Resources\Appointments\RelationManagers\CustomPackagesRelationManager;
use App\Filament\Crm\Resources\Appointments\RelationManagers\CustomerContractsRelationManager;
use App\Filament\Crm\Resources\Appointments\RelationManagers\CustomerInvoiceRelationManager;
use App\Filament\Crm\Resources\Appointments\RelationManagers\CustomerVouchersRelationManager;
use App\Filament\Crm\Resources\Appointments\RelationManagers\VerificationsRelationManager;
use App\Filament\Crm\Resources\Appointments\Pages\ListAppointments;
use App\Filament\Crm\Resources\Appointments\Pages\CreateAppointment;
use App\Filament\Crm\Resources\Appointments\Pages\EditAppointment;
use App\Enums\Appointments\AppointmentDeleteReason;
use App\Enums\Appointments\AppointmentOrderStatus;
use App\Enums\Appointments\AppointmentStatus;
use App\Enums\Appointments\AppointmentType;
use App\Enums\Verifications\VerificationStatus;
use App\Filament\Actions\Table\NeedsVerificationAction;
use App\Filament\Actions\Table\VerifyAction;
use App\Filament\Crm\Resources\Appointments\Forms\AppointmentForm;
use App\Filament\Crm\Resources\Appointments\Schemas\AppointmentTable;
use App\Filament\Crm\Resources\Customers\RelationManagers\CustomerCreditsRelationManager;
use App\Models\Appointment;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Payment;
use Carbon\CarbonImmutable;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Tables\Filters\QueryBuilder;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getModelLabel(): string
    {
        return __('Appointments');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Appointments');
    }

    /**
     * @param  Appointment|null  $record
     */
    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return sprintf('%s / %s / %s / %s',
            $record->customer?->full_name,
            formatDateTime($record->start),
            $record->type->getShortCode(),
            $record->category?->short_code ?? ''
        );
    }

    /**
     * @param  Appointment  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->title;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'start',
            'customer.firstname',
            'customer.lastname',
            'customer.email',
        ];
    }

    /**
     * @param  Appointment  $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('Customer') => $record->customer?->full_name,
            __('Items') => $record->appointmentItems->implode('description', ', '),
        ];
    }

    public static function table(Table $table): Table
    {
        return AppointmentTable::configure($table);
    }

    public static function form(Schema $schema): Schema
    {
        return AppointmentForm::make($schema);
    }

    public static function getRelations(): array
    {
        return [
            AppointmentsRelationManager::class,
            CustomPackagesRelationManager::class,
            CustomerContractsRelationManager::class,
            CustomerInvoiceRelationManager::class,
            CustomerVouchersRelationManager::class,
            CustomerCreditsRelationManager::class,
            VerificationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAppointments::route('/'),
            'create' => CreateAppointment::route('/create'),
            'edit' => EditAppointment::route('/{record}/edit'),
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
