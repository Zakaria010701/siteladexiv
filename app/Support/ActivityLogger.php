<?php

namespace App\Support;

use App\Models\Activity;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\ActivityLogger as SpatieActivityLogger;

class ActivityLogger extends SpatieActivityLogger
{
    public function __construct(SpatieActivityLogger $logger)
    {
        $this->causerResolver = $logger->causerResolver;
        $this->logStatus = $logger->logStatus;
        $this->batch = $logger->batch;
        $this->defaultLogName = $logger->defaultLogName;
        $this->activity = $logger->activity;
    }

    public function performedOn(Model $model): static
    {
        $this->getActivity()->subject()->associate($model);

        if (isset($model->customer)) {
            $this->forCustomer($model->customer);
        }

        return $this;
    }

    public function forCustomer(mixed $customer): static
    {
        $activity = $this->getActivity();

        if (! $customer instanceof Customer) {
            if ($activity instanceof Activity) {
                $activity->customer_id = null;
            }

            return $this;
        }

        if ($activity instanceof Activity) {
            $activity->customer_id = $customer->id;
        }

        return $this;
    }

    public function for(mixed $customer): static
    {
        $this->forCustomer($customer);

        return $this;
    }
}
