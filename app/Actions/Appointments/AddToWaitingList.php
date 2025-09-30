<?php

namespace App\Actions\Appointments;

use Throwable;
use App\Actions\Users\FindAvailableProviders;
use App\Enums\Appointments\AppointmentStatus;
use App\Enums\Appointments\AppointmentType;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use App\Models\WaitingListEntry;
use App\Models\WorkTime;
use App\Support\Appointment\AppointmentCalculator;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AddToWaitingList
{
    public function __construct(
        private CarbonImmutable $wish_date,
        private ?CarbonImmutable $wish_date_till,
        private AppointmentType $appointmentType,
        private Branch $branch,
        private Category $category,
        private Customer $customer,
        private ?User $provider,
        private array $services,
    ) {}

    public static function make(
        string|CarbonInterface $wish_date,
        null|string|CarbonInterface $wish_date_till,
        string|AppointmentType $appointmentType,
        null|string|int|Branch $branch,
        null|string|int|Category $category,
        array|Collection $services,
        int|Customer $customer,
        null|int|User $provider = null,
    ): self {
        // Convert the Date
        if (is_string($wish_date)) {
            $wish_date = CarbonImmutable::parse($wish_date);
        }

        if (is_string($wish_date_till)) {
            $wish_date_till = CarbonImmutable::parse($wish_date_till);
        }

        if (is_string($appointmentType)) {
            $appointmentType = AppointmentType::from($appointmentType);
        }

        // Convert the Branch
        if (is_string($branch) || is_int($branch)) {
            $branch = Branch::findOrFail($branch);
        } elseif (is_null($branch)) {
            $branch = Branch::first();
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

        // Convert the Customer
        if (is_int($provider)) {
            /** @var User */
            $provider = User::findOrFail($provider);
        }

        // Convert the Services
        if ($services instanceof Collection) {
            $services = $services->pluck('id')->toArray();
        }

        $wish_date = $wish_date->toImmutable();
        $wish_date_till = $wish_date_till?->toImmutable();

        return new self(
            wish_date: $wish_date,
            wish_date_till: $wish_date_till,
            appointmentType: $appointmentType,
            branch: $branch,
            category: $category,
            customer: $customer,
            services: $services,
            provider: $provider,
        );
    }

    /**
     * @throws Throwable
     */
    public function execute(): ?WaitingListEntry
    {
        DB::beginTransaction();

        try {
            $entry = $this->createWaitingListEntry();
            $entry->services()->attach($this->services);
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
            throw $e;
        }

        DB::commit();

        return $entry;
    }

    protected function createWaitingListEntry(): WaitingListEntry
    {
        return $this->customer->waitingListEntries()->create([
            'branch_id' => $this->branch->id,
            'appointment_type' => $this->appointmentType,
            'category_id' => $this->category->id,
            'user_id' => $this->provider?->id,
            'wish_date' => $this->wish_date,
            'wish_date_till' => $this->wish_date_till,
        ]);
    }
}
