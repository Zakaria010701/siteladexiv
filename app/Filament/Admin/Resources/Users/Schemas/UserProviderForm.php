<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Filament\Admin\Resources\Users\Schemas\Components\UserBranchesField;
use App\Filament\Admin\Resources\Users\Schemas\Components\UserCategoriesField;
use App\Filament\Admin\Resources\Users\Schemas\Components\UserConsultationCategoriesField;
use App\Filament\Admin\Resources\Users\Schemas\Components\UserIsProviderToggle;
use App\Filament\Admin\Resources\Users\Schemas\Components\UserServicesField;
use App\Filament\Admin\Resources\Users\Schemas\Components\UserShowInFrontendToggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserProviderForm {

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columns(3)
                ->schema(self::components())
        ]);
    }

    public static function step(): Step
    {
        return Step::make(__('Provider'))
            ->icon(Heroicon::Calendar)
            ->columns(3)
            ->schema(self::components());
    }

    public static function components(): array
    {
        return [
            UserIsProviderToggle::make(),
            UserShowInFrontendToggle::make(),
            UserBranchesField::make()
                ->columnStart(1),
            UserCategoriesField::make(),
            UserConsultationCategoriesField::make(),
            UserServicesField::make(),
        ];
    }
}
