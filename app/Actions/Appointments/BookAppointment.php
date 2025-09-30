<?php

namespace App\Actions\Appointments;

use Throwable;
use App\Actions\Users\FindAvailableProviders;
use App\Enums\Appointments\AppointmentStatus;
use App\Enums\Appointments\AppointmentType;
use App\Events\Appointments\AppointmentApprovedEvent;
use App\Events\Appointments\AppointmentCanceledEvent;
use App\Events\Appointments\AppointmentDoneEvent;
use App\Events\Appointments\AppointmentPendingEvent;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Room;
use App\Models\Service;
use App\Models\SystemResource;
use App\Models\User;
use App\Models\WorkTime;
use App\Support\Appointment\AppointmentCalculator;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

readonly class BookAppointment
{
    public function __construct(
        private CarbonImmutable $start,
        private CarbonImmutable $end,
        private AppointmentType $appointmentType,
        private Room $room,
        private Category $category,
        private Customer $customer,
        private Collection $services,
        private User $user,
        private Collection $resources,
        private AppointmentStatus $status,
    ) {}

    public static function make(
        string|CarbonInterface $date,
        string|AppointmentType $appointmentType,
        int|Room $room,
        int|Customer $customer,
        int|User $user,
        int|Category $category,
        array|Collection $services,
        null|array|Collection $resources = null,
        ?int $duration = null,
        AppointmentStatus $status = AppointmentStatus::Pending
    ): self {
        // Convert the Date
        if (is_string($date)) {
            $date = CarbonImmutable::parse($date);
        }

        if (is_string($appointmentType)) {
            $appointmentType = AppointmentType::from($appointmentType);
        }

        // Convert the Category
        if (is_string($category) || is_int($category)) {
            $category = Category::findOrFail($category);
        } elseif (is_null($category)) {
            $category = Category::first();
        }

        // Convert the Customer
        if (is_int($customer)) {
            $customer = Customer::findOrFail($customer);
        }

        // Convert the Room
        if (is_int($room)) {
            $room = Room::findOrFail($room);
        }

        // Convert the Services
        if (is_array($services)) {
            $services = Service::query()
                ->whereIn('id', $services)
                ->get();
        }

        // Convert the Resources
        if (is_array($resources)) {
            $resources = SystemResource::query()
                ->whereIn('id', $resources)
                ->get();
        } elseif (is_null($resources)) {
            $resources = collect();
        }

        // Convert the User
        if (is_int($user)) {
            $user = User::findOrFail($user);
        }

        // Convert duration
        if (is_null($duration)) {
            $duration = CalculateDuration::make($appointmentType, $services)->execute();
        }

        $start = $date->toImmutable();
        $end = $start->addMinutes($duration);

        return new self(
            start: $start,
            end: $end,
            appointmentType: $appointmentType,
            room: $room,
            customer: $customer,
            user: $user,
            category: $category,
            services: $services,
            resources: $resources,
            status: $status,
        );
    }

    /**
     * @throws Throwable
     */
    public function execute(): ?Appointment
    {

        if ($this->checkForOverlapp()) {
            throw new Exception('Overlapp found!');
        }

        DB::beginTransaction();

        try {
            $appointment = $this->createAppointment();

            $this->createAppointmentDetails($appointment);

            $this->dispatchStatusEvent($appointment);
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
            throw $e;
        }

        DB::commit();

        return $appointment;
    }

    private function checkForOverlapp(): bool
    {
        return Appointment::query()
            ->where('start', '<', $this->end)
            ->where('end', '>', $this->start)
            ->where('room_id', $this->room->id)
            ->notCanceled()
            ->status(AppointmentStatus::Pending, '!=')
            ->exists();
    }

    protected function createAppointment(): Appointment
    {
        return $this->customer->appointments()->create([
            'branch_id' => $this->room->branch->id,
            'room_id' => $this->room->id,
            'type' => $this->appointmentType,
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'status' => $this->status,
            'start' => $this->start,
            'end' => $this->end,
        ]);
    }

    protected function createAppointmentDetails(Appointment $appointment): void
    {
        $details = AppointmentCalculator::make($appointment, [
            'services' => $this->services->pluck('id')->toArray(),
            'type' => $this->appointmentType,
            'customer_id' => $this->customer->id,
        ])->updatedCustomer()->updatedServices()->saveData();

        $appointment->appointmentItems()->createMany($details['items']);
        $appointment->appointmentServiceDetails()->createMany($details['serviceDetails']);
        $appointment->discounts()->createMany($details['discounts']);
        $appointment->appointmentOrder()->create($details['appointmentOrder']);

        $appointment->systemResources()->syncWithoutDetaching($this->resources->pluck('id')->toArray());
    }

    protected function dispatchStatusEvent(Appointment $appointment): void
    {
        switch ($appointment->status) {
            case AppointmentStatus::Approved:
                $properties['attributes']['approved_at'] = $appointment->approved_at;
                AppointmentApprovedEvent::dispatch($appointment, auth()->user(), true);
                break;
            case AppointmentStatus::Pending:
                AppointmentPendingEvent::dispatch($appointment, auth()->user(), true);
        }
    }
}
