<?php

namespace App\Filament\Schemas\Components;

use App\Filament\Schemas\App\Filament\Crm\Resources\Customers\Schemas\CompactCustomerForm;
use App\Models\Customer;
use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class CustomerSelect
{
    public static function make(?string $name = 'customer_id', string|Closure|null $relationship = 'customer', ?Closure $modifyQueryUsing = null): Select
    {
        return Select::make($name)
            ->relationship($relationship, 'lastname', $modifyQueryUsing)
            ->getOptionLabelFromRecordUsing(fn (Customer $record) => $record->label)
            ->searchable(['firstname', 'lastname', 'birthday'])
            ->createOptionForm(fn (Schema $schema) => CompactCustomerForm::configure($schema));
    }
}
