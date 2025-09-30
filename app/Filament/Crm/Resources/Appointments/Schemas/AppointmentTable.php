<?php

namespace App\Filament\Crm\Resources\Appointments\Schemas;

use App\Enums\Appointments\AppointmentDeleteReason;
use App\Enums\Appointments\AppointmentOrderStatus;
use App\Enums\Appointments\AppointmentStatus;
use App\Enums\Appointments\AppointmentType;
use App\Enums\Verifications\VerificationStatus;
use App\Filament\Actions\Table\NeedsVerificationAction;
use App\Filament\Actions\Table\VerifyAction;
use App\Filament\Crm\Resources\Customers\CustomerResource;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Payment;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AppointmentTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('canceled_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('done_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('branch.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('room.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('customer.full_name')
                    ->numeric()
                    ->url(fn (Appointment $record): string => isset($record->customer) ? CustomerResource::getUrl('edit', ['record' => $record->customer]) : '')
                    ->sortable()
                    ->searchable(['firstname', 'lastname']),
                TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('appointmentOrder.gross_total')
                    ->label(__('Gross total'))
                    ->money('eur', locale: 'de'),
                TextColumn::make('appointmentOrder.status')
                    ->label(__('Payment'))
                    ->badge(),
                TextColumn::make('payments')
                    ->label(__('Payments'))
                    ->badge()
                    ->formatStateUsing(fn (Payment $state) => $state->badge),
                TextColumn::make('start')
                    ->dateTime(getDateTimeFormat())
                    ->sortable(),
                TextColumn::make('end')
                    ->dateTime(getDateTimeFormat())
                    ->sortable(),
                TextColumn::make('difficulty_score')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()
                    ->color(fn ($state) => $state > 5 ? 'danger' : 'primary')
                    ->sortable(),
                IconColumn::make('complaint')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->state(fn (Appointment $record): ?VerificationStatus => $record->complaint?->verificationStatus())
                    ->tooltip(fn (Appointment $record) => $record->complaint?->appointmentComplaintType?->name),
                IconColumn::make('verified')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->state(fn (Appointment $record): ?VerificationStatus => $record->verificationStatus())
                    ->tooltip(fn (Appointment $record) => $record->currentVerification?->created_at?->format(getDateTimeFormat())),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('type', '!=', AppointmentType::RoomBlock->value))
            ->defaultSort('start', 'desc')
            ->groups([
                Group::make('start')
                    ->label(__('Date'))
                    ->titlePrefixedWithLabel(false)
                    ->date(),
                Group::make('customer_id')
                    ->label(__('Customer'))
                    ->getTitleFromRecordUsing(fn (Appointment $record) => $record->customer?->full_name)
                    ->titlePrefixedWithLabel(false),
            ])
            ->filters(self::filters(), layout: FiltersLayout::AboveContentCollapsible)
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    ActionGroup::make([
                        VerifyAction::make(),
                        NeedsVerificationAction::make(),
                    ])->dropdown(false),
                    ActionGroup::make([
                        VerifyAction::make('verify_complaint')
                            ->label('Complaint verified')
                            ->visible(fn (Appointment $record) => !is_null($record->complaint) && $record->complaint->isNotVerified())
                            ->using(function (array $data, Model $record) {
                                $record->complaint->verifications()->create([
                                    'user_id' => auth()->user()->id,
                                    'status' => VerificationStatus::Pass,
                                    'note' => $data['note'] ?? null,
                                ]);
                            }),
                    ])->dropdown(false),
                    ActionGroup::make([
                        DeleteAction::make()
                            ->before(fn () => null)
                                ->schema([
                                Select::make('delete_reason')
                                    ->live()
                                    ->required()
                                    ->options(AppointmentDeleteReason::class),
                                Textarea::make('delete_note')
                                    ->required(),
                            ])
                            ->using(function (array $data, Appointment $record) {
                                $reason = AppointmentDeleteReason::from($data['delete_reason']);
                                $record->delete();
                            }),
                        ForceDeleteAction::make(),
                        RestoreAction::make(),
                    ])->dropdown(false)
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

    public static function filters(): array
    {
        return [
            QueryBuilder::make()
                ->constraints([
                    DateConstraint::make('start'),
                    DateConstraint::make('end'),
                    SelectConstraint::make('type')
                        ->options(AppointmentType::class),
                    SelectConstraint::make('status')
                        ->options(AppointmentStatus::class),
                ]),
            Filter::make('from')
                ->schema([
                    DatePicker::make('from')
                        ->default(today()->startOfMonth()),
                ])
                ->query(fn (Builder $query, array $data): Builder => $query
                    ->when(
                        $data['from'],
                        fn (Builder $query, $date) => $query->where('start', '>=', Carbon::parse($date)->startOfDay()),
                    )
                )
                ->indicateUsing(function (array $data): ?string {
                    if (empty($data['from'])) {
                        return null;
                    }

                    return __('From: :date', ['date' => formatDate($data['from'])]);
                }),
            Filter::make('until')
                ->schema([
                    DatePicker::make('until')
                        ->default(today()),
                ])
                ->query(fn (Builder $query, array $data): Builder => $query
                    ->when(
                        $data['until'],
                        fn (Builder $query, $date) => $query->where('start', '<=', Carbon::parse($date)->endOfDay()),
                    )
                )
                ->indicateUsing(function (array $data): ?string {
                    if (empty($data['until'])) {
                        return null;
                    }

                    return __('Until: :date', ['date' => formatDate($data['until'])]);
                }),
            SelectFilter::make('branch')
                ->indicator(__('Branch'))
                ->relationship('branch', 'name')
                ->default(auth()->user()->current_branch_id),
            SelectFilter::make('customer')
                ->relationship('customer', 'lastname')
                ->getOptionLabelFromRecordUsing(fn (Customer $record) => $record->full_name)
                ->searchable(['firstname', 'lastname']),
            SelectFilter::make('user')
                ->relationship('user', 'name')
                ->preload()
                ->searchable(),
            SelectFilter::make('type')
                ->options(AppointmentType::class),
            SelectFilter::make('status')
                ->options(AppointmentStatus::class),
            TernaryFilter::make('verified')
                ->nullable()
                ->queries(
                    true: fn (Builder $query) => $query->verified(),
                    false: fn (Builder $query) => $query->notVerified(),
                    blank: fn (Builder $query) => $query,
                ),
            SelectFilter::make('complaint')
                ->options([
                    'no_complaint' => __('No complaint'),
                    'complaint' => __('Complaint'),
                    'complaint_not_verified' => __('Complaint not verified'),
                    'complaint_verified' => __('Complaint verified'),
                ])
                ->query(fn (Builder $query, array $data) => match($data['value']) {
                    'no_complaint' => $query->doesntHave("complaint"),
                    'complaint' => $query->has('complaint'),
                    'complaint_not_verified' => $query->whereHas('complaint', fn (Builder $query) => $query->notVerified()),
                    'complaint_verified' => $query->whereHas('complaint', fn (Builder $query) => $query->verified()),
                    default => $query,
                }),
            SelectFilter::make('payment')
                ->options(AppointmentOrderStatus::class)
                ->query(fn (Builder $query, array $data) => empty($data['value'])
                    ? $query
                    : $query->whereHas('appointmentOrder', fn (Builder $query) => $query->where('status', $data))
                ),
            TrashedFilter::make(),
        ];
    }
}
