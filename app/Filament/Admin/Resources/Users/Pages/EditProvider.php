<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\Schemas\UserProviderForm;
use Filament\Schemas\Schema;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Models\Service;
use Filament\Forms\Components\CheckboxList;
use Filament\Resources\Pages\EditRecord;

class EditProvider extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return __('filament-panels::resources/pages/edit-record.title', [
            'label' => __('Provider'),
        ]);
    }

    public static function getNavigationLabel(): string
    {
        return __('Provider');
    }

    public function getBreadcrumb(): string
    {
        return __('Provider');
    }

    public function form(Schema $schema): Schema
    {
        return UserProviderForm::configure($schema);

    }
}
