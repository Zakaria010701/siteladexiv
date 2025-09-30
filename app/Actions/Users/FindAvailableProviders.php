<?php

namespace App\Actions\Users;

use App\Enums\Appointments\AppointmentType;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FindAvailableProviders
{
    public function __construct(
        private AppointmentType $appointmentType,
        private Branch $branch,
        private Category $category,
        private Collection $services,
    ) {}

    public static function make(
        string|AppointmentType $appointmentType,
        int|string|Branch $branch,
        int|string|Category $category,
        array|Collection $services
    ): self {
        if (is_string($appointmentType)) {
            $appointmentType = AppointmentType::from($appointmentType);
        }

        if (is_string($branch) || is_int($branch)) {
            $branch = Branch::find($branch);
        }

        if (is_string($category) || is_int($category)) {
            $category = Category::find($category);
        }

        if (is_array($services)) {
            $services = Service::whereIn('id', $services)->get();
        }

        return new self(
            $appointmentType,
            $branch,
            $category,
            $services,
        );
    }

    public function execute(): Collection
    {
        return User::query()
            ->whereHas('branches', fn (Builder $query) => $query->where('branches.id', $this->branch->id))
            ->when(frontend()->booking_constraint_by_category, function (Builder $query) {
                $query->when(
                    value: $this->appointmentType->isConsultation(),
                    callback: function (Builder $query) {
                        $query->whereHas('consultationCategories', fn (Builder $query) => $query->where('consultationCategories.id', $this->category->id));
                    },
                    default: function (Builder $query) {
                        $query->whereHas('categories', fn (Builder $query) => $query->where('categories.id', $this->category->id));
                    }
                );
            })
            ->when(frontend()->booking_constraint_by_services, function (Builder $query) {
                $query->whereHas('services', fn (Builder $query) => $query->whereIn('services.id', $this->services->pluck('id')), '>=', $this->services->count());
            })
            ->get();
    }
}
