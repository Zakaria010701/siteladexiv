<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Filament\Admin\Resources\Users\Schemas\Components\UserOccupationSection;
use App\Filament\Admin\Resources\Users\Schemas\Components\UserQuestionnareSection;
use App\Filament\Admin\Resources\Users\Schemas\Components\UserTaxDataSection;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserDetailsForm {

    public static function configure(Schema $schema): Schema
    {
        return $schema->components(self::components());
    }

    public static function step(): Step
    {
        return Step::make(__('Details'))
            ->icon(Heroicon::ListBullet)
            ->schema(self::components());
    }

    public static function components(): array
    {
        return [
            UserQuestionnareSection::make(),
            UserTaxDataSection::make(),
            UserOccupationSection::make(),
        ];
    }
}
