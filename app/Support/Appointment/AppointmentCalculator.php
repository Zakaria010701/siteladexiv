<?php

namespace App\Support\Appointment;

use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Actions\Appointments\CalculateDuration;
use App\Enums\Appointments\AppointmentItemType;
use App\Enums\Appointments\AppointmentOrderStatus;
use App\Enums\Appointments\AppointmentType;
use App\Enums\Gender;
use App\Enums\Transactions\DiscountType;
use App\Models\Appointment;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\CustomerDiscount;
use App\Models\DiscountTemplate;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Support\Calculator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AppointmentCalculator
{
    private ?Customer $customer;

    private Collection $items;

    private Collection $serviceDetails;

    private Collection $serviceCredits;

    private Collection $servicePackageCredits;

    private Collection $customerCredits;

    private Collection $discounts;

    private Collection $payments;

    private Collection $selectedPackages;

    private array $contracts;

    private array $services;

    private array $servicePackages;

    private array $order;

    private AppointmentType $type;

    private int $duration;

    private ?Appointment $lastAppointment;

    public function __construct(
        private readonly ?Appointment $record,
        private array $data,
        private readonly string $path,
    ) {}

    public static function make(?Appointment $record, array $data, string $path = ''): self
    {
        return (new self($record, $data, $path))->loadData();
    }

    public function getData()
    {
        return $this->data;
    }

    private function get(string $path): mixed
    {
        return data_get($this->data, is_array($this->data) ? $path : "{$this->path}.{$path}");
    }

    private function set(string $path, mixed $value): mixed
    {
        return data_set($this->data, $path, $value);
    }

    public function loadData(): self
    {
        $this->items = collect($this->get('items') ?? []);
        $this->serviceDetails = collect($this->get('serviceDetails') ?? []);
        $this->discounts = collect($this->get('discounts') ?? []);
        $this->payments = collect($this->get('payments') ?? []);
        $this->services = $this->get('services') ?? [];
        $this->contracts = $this->get('contracts') ?? [];
        $this->order = $this->get('appointmentOrder') ?? [];
        $type = $this->get('type');
        $this->duration = $this->get('end') ?? 0;
        $this->type = ($type instanceof AppointmentType) ? $type : AppointmentType::from($type);
        $this->customer = Customer::find($this->get('customer_id'));
        $this->servicePackages = $this->get('service_packages') ?? [];

        return $this;
    }

    public function saveData(): array
    {
        $this->set('items', $this->items->toArray());
        $this->set('serviceDetails', $this->serviceDetails->toArray());
        $this->set('discounts', $this->discounts->toArray());
        $this->set('appointmentOrder', $this->order);
        $this->set('end', $this->duration);
        $this->set('contracts', $this->contracts);
        if(isset($this->customer)) {
            $this->set('contract_select_actions', $this->customer->contracts()->unused()->get()->mapWithKeys(fn (Contract $contract) => [
                Str::uuid()->toString() => [
                    'id' => $contract->id,
                    'title' => $contract->label,
                ]
            ])->toArray());
        } else {
            $this->set('contract_select_actions', []);
        }

        return $this->getData();
    }

    public function calculate(): self
    {
        $this->updateItems();
        $this->updateServiceDetails();
        $this->updateOrder();

        return $this;
    }

    public function updatedPrices(): self
    {
        $this->updateSelectedPackages();
        $this->updateItems();
        $this->updateServiceDetails();
        $this->applyDiscounts();
        $this->updateOrder();

        return $this;
    }

    public function updatedServices(): self
    {
        $this->updateItems();
        $this->updateServiceDetails();
        $this->updateServicePackages();
        $this->applyDiscounts();
        $this->updateOrder();
        $this->updateDuration();

        $this->set('service_packages', $this->servicePackages);
        $this->set('description', Service::whereIn('id', $this->services)->pluck('short_code')->implode(', '));

        return $this;
    }

    public function updatedCustomer(): self
    {
        $this->customer = Customer::find($this->get('customer_id'));

        if (is_null($this->customer)) {
            return $this;
        }

        $this->getLastAppointment();
        $this->updateServiceCredits();

        if (isset($this->lastAppointment)) {
            $this->set('treatment_type_id', $this->lastAppointment->treatment_type_id);
        }

        $this->set('birthday', $this->customer->birthday);
        $this->set('serviceCredits', $this->serviceCredits->toArray());
        $this->set('servicePackageCredits', $this->servicePackageCredits->toArray());
        $this->set('customerCredits', $this->customerCredits->toArray());

        return $this;
    }

    public function createdContract()
    {
        $this->updateServiceCredits();

        return $this;
    }

    private function updateItems(): void
    {
        $this->removeMissingServiceItems();
        $this->addNewServiceItems();
        $this->updateServiceUsed();
        $this->updateContractItems();
        $this->updateConsultationItem();
    }

    private function removeMissingServiceItems(): void
    {
        //Remove all service items that aren't in the $services array
        $this->items = $this->items->reject(fn (array $item) => (
            $item['purchasable_type'] == Service::class
            && ! in_array($item['purchasable_id'], $this->services)
        ));
    }

    private function addNewServiceItems(): void
    {
        //Find all services in the $services array that aren't already in the collection
        $newServices = array_diff($this->services, $this->items->where('purchasable_type', Service::class)->pluck('purchasable_id')->toArray());
        if (empty($newServices)) {
            return;
        }

        //Add the missing services with default values
        $add = Service::query()
            ->withCount([
                /* @phpstan-ignore-next-line */
                'serviceCredits' => fn (Builder $query) => $query->where('customer_id', $this->customer?->id)->whereNotNull('customer_id')->unused(),
            ])
            ->whereIn('id', $newServices)
            ->get()
            ->mapWithKeys(function (Service $item): array {
                $price = (float) $item->price;

                $purchased = ($this->type === AppointmentType::Consultation) ? 0 : 1;

                return [(string) Str::uuid() => [
                    'purchasable_type' => Service::class,
                    'purchasable_id' => $item->id,
                    'type' => AppointmentItemType::Service->value,
                    'description' => $item->name,
                    'unit_price' => $price,
                    'used' => 0,
                    'quantity' => 1,
                    'purchased' => $purchased,
                    'discount_total' => 0,
                    'sub_total' => $price * $purchased,
                    'meta' => [],
                ]];
            });

        $this->items = $this->items->merge($add);
    }

    private function updateServiceUsed(): void
    {
        $this->items = $this->items->mapWithKeys(function (array $item, string $key): array {
            if ($item['purchasable_type'] != Service::class) {
                return [$key => $item];
            }

            $details = $this->serviceDetails->where('service_id', $item['purchasable_id'])->first();

            if(empty($details)) {
                return [$key => $item];
            }

            $used = 0;
            $creditCount = $this->customer->serviceCredits()
                ->where('service_id', $item['purchasable_id'])
                ->unused()
                ->count();
            $credit = $details['use_credit'] ?? ($creditCount > 0 ? 1 : 0);

            if($credit && $creditCount > $used) {
                $used++;
            }

            if($used == 0) {
                $item['used'] = 0;
            } else {
                $item['used'] = ($item['used'] < $used) ? $used : $item['used'];
            }

            $item['purchased'] = $item['quantity'] - $item['used'];
            $item['sub_total'] = $item['unit_price'] * $item['purchased'];

            return [$key => $item];
        });
    }

    private function updateContractItems(): void
    {
        if ($this->record == null) {
            return;
        }
        $contracts = $this->record->contracts()->get();
        $contracts->each(function (Contract $contract) {
            $items = $this->items
                ->where('purchasable_type', Contract::class)
                ->where('purchasable_id', $contract->id);

            $key = $items->keys()->first();
            $item = $items->first();

            if (empty($item)) {
                $this->items->push([
                    'purchasable_type' => Contract::class,
                    'purchasable_id' => $contract->id,
                    'type' => AppointmentItemType::Contract->value,
                    'description' => __('Contract :type :date', ['type' => $contract->type->getLabel(), 'date' => formatDate($contract->date)]),
                    'unit_price' => $contract->price,
                    'used' => 0,
                    'quantity' => 1,
                    'purchased' => 1,
                    'discount_total' => 0,
                    'sub_total' => $contract->price,
                    'meta' => [],
                ]);

                return;
            }

            $item['price'] = $contract->price;

            $this->items->put($key, $item);
        });

        $this->contracts = $contracts->pluck('id')->toArray();

        // Reject all unfiting Contract Items
        $contractIds = $contracts->pluck('id')->toArray();
        $this->items = $this->items->reject(function (array $item) use ($contractIds): bool {
            if ($item['type'] != AppointmentItemType::Contract->value) {
                return false;
            }

            if (
                $item['purchasable_type'] == null
                || $item['purchasable_id'] == null
                || $item['purchasable_type'] != Contract::class
            ) {
                return true;
            }
            if (in_array($item['purchasable_id'], $contractIds)) {
                return false;
            }

            return true;
        });
    }

    private function updateConsultationItem(): void
    {
        //Remove all appointment type items, that aren't consultation
        /*$this->items = $this->items->reject(fn (array $item): bool => is_null($item['purchasable_type'])
            && ! empty($item['meta']['type'])
            && $item['meta']['type'] != AppointmentType::Consultation->value);*/

        if(!$this->type->hasConsultationFee()) {
            $this->items = $this->items->reject(fn (array $item) => $item['type'] == AppointmentItemType::ConsultationFee->value);
            return;
        }

        if (! appointment()->consultation_fee_enabled) {
            return;
        }

        // If a consultation item already exists do nothing
        if ($this->items->filter(fn (array $item): bool => $item['type'] == AppointmentItemType::ConsultationFee->value)->isNotEmpty()) {
            return;
        }

        $this->items = $this->items->push([
            'purchasable_type' => null,
            'purchasable_id' => null,
            'type' => AppointmentItemType::ConsultationFee->value,
            'description' => __('Consultation'),
            'unit_price' => appointment()->consultation_fee,
            'quantity' => 1,
            'used' => 0,
            'purchased' => 1,
            'discount_total' => 0,
            'sub_total' => appointment()->consultation_fee, // TODO: Add setting to allow modification
            'meta' => ['type' => AppointmentType::Consultation->value],
        ]);
    }

    private function updateServiceDetails(): void
    {
        $this->removeMissingServiceDetails();
        $this->addNewServiceDetails();
    }

    private function removeMissingServiceDetails()
    {
        //Remove all service items that aren't in the $services array
        $this->serviceDetails = $this->serviceDetails->reject(fn (array $item) => ! in_array($item['service_id'], $this->services));
    }

    private function addNewServiceDetails()
    {
        //Find all services in the $services array that aren't already in the collection
        $newServices = array_diff($this->services, $this->serviceDetails->pluck('service_id')->toArray());
        if (empty($newServices)) {
            return;
        }

        //Add the missing services with default values
        $add = Service::query()
            ->whereIn('id', $newServices)
            ->get()
            ->mapWithKeys(function (Service $item): array {
                return [(string) Str::uuid() => [
                    'is_completed' => true,
                    'use_credit' => $this->customer->serviceCredits()
                        ->where('service_id', $item->id)
                        ->unused()
                        ->exists(),
                    'service_id' => $item->id,
                ]];
            });

        $this->serviceDetails = $this->serviceDetails->merge($add);
    }

    private function updateOrder(): void
    {
        $this->order['base_total'] = $this->items->sum('sub_total');
        $this->order['discount_total'] = $this->discounts->sum('amount');
        $this->order['tax_total'] = 0;
        $this->order['net_total'] = 0;
        $this->order['gross_total'] = $this->order['base_total'] - $this->order['discount_total'];
        $this->order['paid_total'] = $this->payments->sum('amount');
        $this->order['status'] = ($this->order['paid_total'] < $this->order['gross_total']) ? AppointmentOrderStatus::Open : AppointmentOrderStatus::Paid;
    }

    private function updateDuration(): void
    {
        $this->duration = CalculateDuration::make($this->type, $this->services)->execute();
    }

    private function storeFromForm(Get $get, Set $set, string $path = ''): void
    {
        $set($path.'items', $this->items->toArray());
        $set($path.'serviceDetails', $this->serviceDetails->toArray());
        $set($path.'discounts', $this->discounts->toArray());
        $set($path.'appointmentOrder', $this->order);
        $set($path.'end', $this->duration);
    }

    public function fromArray(array $data): array
    {
        $this->serviceDetails = collect($data['serviceDetails'] ?? []);
        $this->items = collect($data['items'] ?? []);
        $this->discounts = collect($data['discounts'] ?? []);
        $this->payments = collect($data['payments'] ?? []);
        $this->services = $data['services'] ?? [];
        $this->order = $data['appointmentOrder'] ?? [];
        $type = $data['type'];
        $this->type = ($type instanceof AppointmentType) ? $type : AppointmentType::from($type);
        $this->customer = Customer::find($data['customer_id']);
        $this->servicePackages = $data['service_packages'];

        $this->updateItems();
        $this->updateServiceDetails();
        empty($this->servicePackages) ? $this->updateServicePackages() : $this->updateSelectedPackages();
        $this->applyDiscounts();
        $this->updateOrder();
        $this->updateServiceCredits();
        $this->updateDuration();

        $data['serviceDetails'] = $this->serviceDetails->toArray();
        $data['items'] = $this->items->toArray();
        $data['discounts'] = $this->discounts->toArray();
        $data['appointmentOrder'] = $this->order;
        $data['serviceCredits'] = $this->serviceCredits->toArray();
        $data['servicePackageCredits'] = $this->servicePackageCredits->toArray();
        $data['customerCredits'] = $this->customerCredits->toArray();
        $data['end'] = $this->duration;
        $data['service_packages'] = $this->servicePackages;

        return $data;
    }

    private function updateServicePackages(): void
    {
        $gender = $this->customer?->gender;

        $this->servicePackages = [];
        $this->selectedPackages = collect();

        $packages = ServicePackage::query()
            ->with('services')
            ->whereHas('services', fn (Builder $query) => $query->whereIn('services.id', $this->services))
            ->whereDoesntHave('services', fn (Builder $query) => $query->whereNotIn('services.id', $this->services))
            ->where(fn (Builder $query) => $query
                ->whereNull('customer_id')
                ->when($this->customer, fn (Builder $query) => $query->orWhere('customer_id', $this->customer->id))
            )
            ->when(
                $gender != Gender::NonBinary && ! is_null($gender),
                fn (Builder $query) => $query->whereIn('gender', [$gender->value, Gender::NonBinary->value])
            )
            ->get()
            ->map(fn (ServicePackage $package) => [
                'id' => $package->id,
                'services' => $package->services->pluck('id')->toArray(),
                'services_count' => $package->services->count(),
                'name' => $package->name,
                'short_code' => $package->short_code,
                'customer_id' => $package->customer_id,
                'discount_percentage' => $package->discount_percentage,
                'discount' => $package->discount,
                'price' => $package->price,
            ])
            ->toBase()
            ->reject(fn (array $package) => $package['services_count'] <= 0)
            ->sortByDesc('services_count');

        if ($packages->isEmpty()) {
            return;
        }

        while ($packages->isNotEmpty()) {
            // Prefer Custom Packages over regular packages
            $select = $packages->whereNotNull('customer_id')->first() ?? $packages->first();
            $this->servicePackages[] = $select['id'];
            $this->selectedPackages->push($select);
            $packages = $packages->reject(function (array $package) use ($select) {
                return ! empty(array_intersect($package['services'], $select['services']));
            });
        }

    }

    private function updateSelectedPackages(): void
    {
        $this->selectedPackages = ServicePackage::query()
            ->whereIn('id', $this->servicePackages)
            ->get()
            ->map(fn (ServicePackage $package) => [
                'id' => $package->id,
                'services' => $package->services->pluck('id')->toArray(),
                'services_count' => $package->services->count(),
                'name' => $package->name,
                'short_code' => $package->short_code,
                'discount_percentage' => $package->discount_percentage,
                'discount' => $package->discount,
                'price' => $package->price,
                'custom' => false,
            ]);
    }

    private function applyDiscounts(): void
    {
        $this->applyPackageTemplateDiscount();
        //$this->applyCustomerDiscounts();
        $this->applySelectedPackageDiscounts();
        $this->applyQuantityDiscount();
        $this->applyCustomDiscounts();
        $this->removeEmptyDiscounts();
    }

    private function applyPackageTemplateDiscount(): void
    {
        //Get applicable items count
        $count = $this->items->where('purchasable_type', Service::class)->where('purchased', '>', 0)->count();
        /** @var DiscountTemplate|null $template */
        $template = DiscountTemplate::query()
            //->with(['categories' => fn (BelongsToMany $query) => $query->where('category_id', $this->category_id)])
            ->where('type', DiscountType::PackageTemplate->value)
            ->where('quantity', '<=', $count)
            ->orderByDesc('quantity')
            ->first();


        if (is_null($template)) {
            $this->removePackageDiscount();

            return;
        }

        //$template = $template->categories->first() ?? $template;

        $percentage = $template->pivot?->percentage ?? $template->percentage;

        $base = $this->items->where('purchasable_type', Service::class)->sum('sub_total');
        $amount = $template->amount ?? Calculator::getDiscountAmmount($base, $percentage);

        $key = $this->discounts->where('type', DiscountType::PackageTemplate->value)->keys()->first();
        $discount = $this->discounts->where('type', DiscountType::PackageTemplate->value)->first() ?? ['description' => DiscountType::PackageTemplate->getLabel()];
        $discount['source_type'] = $template::class;
        $discount['source_id'] = $template->id;
        $discount['type'] = DiscountType::PackageTemplate->value;
        $discount['percentage'] = $percentage;
        $discount['amount'] = $amount;
        $discount['permanent'] = false;
        $this->discounts->put($key, $discount);
    }

    private function removePackageDiscount(): void
    {
        $this->discounts = $this->discounts->reject(fn (array $item) => $item['type'] == DiscountType::PackageTemplate->value);
    }

    private function applySelectedPackageDiscounts(): void
    {
        // Dont apply package discounts for Consultation Appointments
        if ($this->type == AppointmentType::Consultation) {
            $this->discounts = $this->discounts->reject(fn (array $item) => $item['type'] == DiscountType::Package->value);

            return;
        }

        if ($this->selectedPackages->isEmpty()) {
            return;
        }

        $selected = $this->selectedPackages
            ->reject(fn (array $package) => empty($package['discount_percentage']) && empty($package['discount']) && empty($package['price']));
        $selectedIds = $selected->whereNotNull('id')->pluck('id')->values()->toArray();

        $this->discounts = $this->discounts->reject(fn (array $item) => $item['type'] == DiscountType::Package->value && ! in_array($item['source_id'], $selectedIds));

        $selected
            ->each(function (array $package) {
                $services = $this->items
                    ->where('purchased', '>', 0)
                    ->where('purchasable_type', Service::class)
                    ->whereIn('purchasable_id', $package['services']);

                $amount = 0;
                $percentage = null;

                $service_sum = $services->sum('sub_total');
                if (! empty($package['discount'])) {
                    $amount = $package['discount'];
                } elseif (! empty($package['discount_percentage'])) {
                    $percentage = $package['discount_percentage'];

                    $amount = Calculator::getDiscountAmmount($service_sum, $percentage);
                } else {
                    $amount = $service_sum - $package['price'];
                }

                if($amount > $service_sum) {
                    $amount = $service_sum;
                }

                $key = $this->discounts
                    ->where('type', DiscountType::Package->value)
                    ->where('source_id', $package['id'])
                    ->keys()->first();
                $discount = [
                    'source_type' => ServicePackage::class,
                    'source_id' => $package['id'],
                    'description' => $package['name'],
                    'type' => DiscountType::Package->value,
                    'percentage' => $percentage,
                    'permanent' => false,
                    'amount' => $amount,
                ];
                $this->discounts->put($key, $discount);
            });
    }

    private function applyQuantityDiscount(): void
    {
        $quantityDiscounts = DiscountTemplate::query()
            //->with(['categories' => fn (BelongsToMany $query) => $query->where('category_id', $this->category_id)])
            ->where('type', DiscountType::Quantity->value)
            ->get();

        $this->resetQuantityDiscounts();

        $quantityDiscounts->each(function (DiscountTemplate $discount) use ($quantityDiscounts) {
            //$percentage = $discount->categories->first()?->pivot?->percentage ?? $discount->percentage;
            $percentage = $discount->percentage;
            $higher = $quantityDiscounts->where('items', '>', $discount->quantity)->sortBy('items')->first()?->quantity;
            $base = $this->items
                ->where('purchasable_type', Service::class)
                ->where('purchased', '>=', $discount->quantity)
                ->when($higher, fn (Collection $collection) => $collection->where('purchased', '<', $higher))
                ->sum('sub_total');
            $amount = Calculator::getDiscountAmmount($base, floatval($percentage));
            //$source = $discount->categories->first() ?? $discount;
            $source = $discount;

            $key = $this->discounts
                ->where('type', DiscountType::Quantity->value)
                ->where('percentage', $percentage)
                ->keys()
                ->first();
            $discount = $this->discounts
                ->where('type', DiscountType::Quantity->value)
                ->where('percentage', $percentage)
                ->first() ?? [
                    'type' => DiscountType::Quantity->value,
                    'description' => DiscountType::Quantity->getLabel().sprintf(' %s %s', $discount->quantity, __('Pcs.')),
                    'percentage' => $percentage,
                ];

            $discount['source_type'] = $source::class;
            $discount['source_id'] = $source->id;
            $discount['permanent'] = false;
            $discount['amount'] = $amount;
            $this->discounts->put($key, $discount);
        });

        $this->removeQuantityDiscounts();
    }

    private function resetQuantityDiscounts(): void
    {
        $this->discounts = $this->discounts->map(function (array $item) {
            if ($item['type'] != DiscountType::Quantity->value) {
                return $item;
            }

            $item['amount'] = 0;

            return $item;
        });
    }

    private function removeQuantityDiscounts(): void
    {
        $this->discounts = $this->discounts->reject(fn (array $item) => $item['type'] == DiscountType::Quantity->value && $item['amount'] == 0);
    }

    private function applyCustomDiscounts(): void
    {
        if ($this->discounts->where('type', DiscountType::Custom->value)->isEmpty()) {
            return;
        }

        $base = $this->items->sum('sub_total');
        $this->discounts = $this->discounts
            //->reject(fn (array $item) => $item['type'] != DiscountType::Custom->value)
            ->map(function (array $item) use ($base) {
                if (empty($item['percentage'])) {
                    return $item;
                }
                if (! empty($item['source_type'])) {
                    return $item;
                }

                $item['amount'] = Calculator::getDiscountAmmount($base, $item['percentage']);

                return $item;
            });
    }

    private function removeEmptyDiscounts(): void
    {
        $this->discounts = $this->discounts->reject(function (array $item) {
            if ($item['amount'] > 0) {
                return false;
            }
            if (! in_array($item['type'], [DiscountType::Package->value, DiscountType::Quantity->value])) {
                return false;
            }

            return true;
        });
    }

    private function updateServiceCredits(): void
    {
        if (is_null($this->customer)) {
            $this->serviceCredits = collect();
            $this->servicePackageCredits = collect();
            $this->customerCredits = collect();

            return;
        }

        $this->serviceCredits = Service::query()
            ->withCount([
                /* @phpstan-ignore-next-line */
                'serviceCredits' => fn (Builder $query) => $query->where('customer_id', $this->customer->id)->unused(),
            ])
            ->whereHas('serviceCredits', fn (Builder $query) => $query->where('customer_id', $this->customer->id))
            //->orWhereHas('appointmentItems', fn (Builder $query) => $query)
            ->get()
            ->map(fn (Service $service) => [
                'service' => $service->id,
                'category' => $service->category_id,
                'name' => $service->name,
                'open' => $service->service_credits_count,
            ])
            ->sortBy('open', descending: true);

        $gender = $this->customer?->gender;

        $openCredits = $this->serviceCredits->reject(fn (array $item) => $item['open'] <= 0)->pluck('service');
        $this->servicePackageCredits = ServicePackage::query()
            ->with('services')
            ->whereHas('services', fn (Builder $query) => $query->whereIn('services.id', $openCredits))
            ->whereDoesntHave('services', fn (Builder $query) => $query->whereNotIn('services.id', $openCredits))
            ->where(fn (Builder $query) => $query
                ->whereNull('customer_id')
                ->orWhere('customer_id', $this->customer->id)
            )
            ->when(
                $gender != Gender::NonBinary && ! is_null($gender),
                fn (Builder $query) => $query->whereIn('gender', [$gender->value, Gender::NonBinary->value])
            )
            ->get()
            ->map(fn (ServicePackage $service) => [
                'package' => $service->id,
                'category' => $service->category_id,
                'name' => sprintf('%s (%s)', $service->short_code, $service->services->implode('short_code', ', ')),
                'services' => $service->services->pluck('id')->toArray(),
            ]);

        $packageServices = $this->servicePackageCredits->pluck('services')->flatten()->unique()->toArray();
        $this->serviceCredits = $this->serviceCredits->reject(fn (array $item) => in_array($item['service'], $packageServices));

        $this->customerCredits = $this->customer->customerCredits()
            ->whereNull('spent_at')
            ->limit(3)
            ->get()
            ->map(fn (CustomerCredit $credit) => [
                'name' => sprintf('%s %s', formatDate($credit->created_at), formatMoney($credit->amount)),
                'open' => $credit->open_amount,
            ]);
    }

    private function getLastAppointment(): void
    {
        $customer_id = $this->get('customer_id');
        $start = $this->get('start') ?? today()->format('Y-m-d H:i');

        if ($customer_id === null) {
            return;
        }

        $this->lastAppointment = Appointment::query()
            ->with([
                'appointmentDetail',
            ])
            ->where('customer_id', $customer_id)
            ->when($this->record, fn (Builder $query) => $query->where('id', '!=', $this->record->id))
            ->where('start', '<', $start)
            ->latest('start')
            ->first();
    }

    private function applyCustomerDiscounts(): void
    {
        if ($this->discounts->where('permanent', true)->isNotEmpty()) {
            return;
        }

        $services = $this->items->where('purchasable_type', Service::class)
            ->mapWithKeys(fn (array $item) => [
                $item['purchasable_id'] => [
                    'quantity' => $item['quantity'],
                ],
            ])
            ->toArray();
        $servicesCount = count($services);

        if (is_null($this->customer)) {
            return;
        }

        /** @var null|CustomerDiscount $cDiscount */
        $cDiscount = $this->customer->customerDiscounts()
            ->with('services')
            ->get()
            ->filter(function (CustomerDiscount $discount) use ($services, $servicesCount) {
                $sCount = $discount->services->count();
                if ($sCount != $servicesCount) {
                    return false;
                }

                $count = $discount->services->reject(fn ($service) => empty($services[$service->id])
                    || $services[$service->id]['quantity'] != $service->pivot->quantity)
                    ->count();

                return $count == $sCount;
            })
            ->sortByDesc(fn (CustomerDiscount $discount) => $discount->services->count())
            ->first();

        if (is_null($cDiscount)) {
            $this->discounts = $this->discounts->reject(fn (array $item) => $item['type'] == DiscountType::Customer->value);

            return;
        }

        $base = $this->items->where('purchasable_type', Service::class)->sum('sub_total');
        $amount = $cDiscount->amount ?? Calculator::getDiscountAmmount($base, $cDiscount->percentage);
        $this->discounts = $this->discounts->reject(fn (array $item) => $item['type'] != DiscountType::Customer->value);

        $key = $this->discounts->where('type', DiscountType::Customer->value)->keys()->first();
        $discount = $this->discounts->where('type', DiscountType::Customer->value)->first() ?? ['description' => $cDiscount->description];
        $discount['source_type'] = $cDiscount::class;
        $discount['source_id'] = $cDiscount->id;
        $discount['type'] = DiscountType::Customer->value;
        $discount['percentage'] = $cDiscount->percentage;
        $discount['permanent'] = false;
        $discount['amount'] = $amount;
        $this->discounts->put($key, $discount);
    }
}
