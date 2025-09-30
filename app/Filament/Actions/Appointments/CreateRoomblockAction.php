<?php

namespace App\Filament\Actions\Appointments;

use Filament\Support\Enums\Width;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Section;
use App\Actions\Appointments\BookAppointment;
use App\Actions\Appointments\CalculateDuration;
use App\Enums\Appointments\AppointmentStatus;
use App\Enums\Appointments\AppointmentType;
use App\Enums\Gender;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use App\Filament\Crm\Resources\Customers\Forms\CustomerForm;
use App\Filament\Schemas\Components\CustomerSelect;
use App\Forms\Components\ItemActions;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\WorkTime;
use App\Support\Appointment\AppointmentCalculator;
use App\Support\Appointment\BookingCalculator;
use App\Support\AvailabilitySupport;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Icon;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class CreateRoomblockAction extends CreateAction
{
    public static function getDefaultName(): ?string
    {
        return 'create-roomblock';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Roomblock'));

        $this->authorize(AppointmentResource::canCreate());

        $this->model(Appointment::class);

        $this->modalHeading(__('Roomblock'));

        $this->modalIcon(Heroicon::Plus);

        $this->mutateDataUsing(function (array $data): array {
            $data['status'] = AppointmentStatus::Pending;

            return $data;
        });

        $this->schema([
            DateTimePicker::make('start')
                ->default(now()->endOfHour()->addSecond())
                ->dehydrated()
                ->required(),
            TextInput::make('end')
                ->label(__('Duration'))
                ->required()
                ->numeric()
                ->dehydrateStateUsing(fn ($state, Get $get): Carbon => Carbon::parse($get('start'))->addMinutes($state)),
            Select::make('type')
                ->disabled()
                ->dehydrated()
                ->options(AppointmentType::class)
                ->default(AppointmentType::RoomBlock->value)
                ->required(),
            Select::make('branch_id')
                ->relationship('branch', 'name')
                ->default(auth()->user()->current_branch_id)
                ->disabled()
                ->dehydrated()
                ->required(),
            Select::make('room_id')
                ->relationship('room', 'name', modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('branch_id', $get('branch_id')))
                ->required(),
            Select::make('user_id')
                ->relationship('user', 'name')
                ->default(auth()->id())
                ->searchable()
                ->preload()
                ->required(),
            Textarea::make('description')
                ->columnSpanFull(),
        ]);
    }
}
