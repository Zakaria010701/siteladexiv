<?php

namespace App\Filament\Crm\Resources\Customers;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Filament\Crm\Resources\Customers\RelationManagers\AppointmentsRelationManager;
use App\Filament\Crm\Resources\Customers\RelationManagers\ContractsRelationManager;
use App\Filament\Crm\Resources\Customers\RelationManagers\CustomerCreditsRelationManager;
use App\Filament\Crm\Resources\Customers\RelationManagers\InvoicesRelationManager;
use App\Filament\Crm\Resources\Customers\RelationManagers\VouchersRelationManager;
use App\Filament\Crm\Resources\Customers\RelationManagers\ChildrenRelationManager;
use App\Filament\Crm\Resources\Customers\RelationManagers\CustomPackagesRelationManager;
use App\Filament\Crm\Resources\Customers\RelationManagers\VerificationsRelationManager;
use App\Filament\Crm\Resources\Customers\Pages\ListCustomers;
use App\Filament\Crm\Resources\Customers\Pages\CreateCustomer;
use App\Filament\Crm\Resources\Customers\Pages\EditCustomer;
use App\Enums\Verifications\VerificationStatus;
use App\Filament\Actions\Table\EditPassword;
use App\Filament\Actions\Table\NeedsVerificationAction;
use App\Filament\Actions\Table\VerifyAction;
use App\Filament\Crm\Resources\CustomerResource\Pages;
use App\Filament\Crm\Resources\CustomerResource\RelationManagers;
use App\Filament\Schemas\App\Filament\Crm\Resources\Customers\Schemas\CustomerForm;
use App\Models\Contract;
use App\Models\Customer;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getModelLabel(): string
    {
        return __('Customer');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Customers');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'firstname',
            'lastname',
            'email',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('Email') => $record->email,
            __('Phone') => $record->phone_number,
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return CustomerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime(getDateTimeFormat())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime(getDateTimeFormat())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('gender')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('firstname')
                    ->searchable(),
                TextColumn::make('lastname')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                PhoneColumn::make('phone_number')
                    ->displayFormat(PhoneInputNumberType::INTERNATIONAL),
                TextColumn::make('birthday')
                    ->searchable()
                    ->date(getDateFormat()),
                IconColumn::make('verified')
                    ->state(fn (Model $record): VerificationStatus => $record->verificationStatus())
                    ->tooltip(fn (Model $record) => $record->currentVerification?->created_at?->format(getDateTimeFormat())),
            ])
            ->filters([
                TernaryFilter::make('verified')
                    ->nullable()
                    ->queries(
                        true: fn (Builder $query) => $query->verified(),
                        false: fn (Builder $query) => $query->notVerified(),
                        blank: fn (Builder $query) => $query,
                    ),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    VerifyAction::make(),
                    NeedsVerificationAction::make(),
                    EditPassword::make()
                        ->visible(auth()->user()->can('admin', Customer::class)),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AppointmentsRelationManager::class,
            ContractsRelationManager::class,
            CustomerCreditsRelationManager::class,
            InvoicesRelationManager::class,
            VouchersRelationManager::class,
            ChildrenRelationManager::class,
            CustomPackagesRelationManager::class,
            VerificationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'edit' => EditCustomer::route('/{record}/edit'),
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
