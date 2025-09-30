<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use App\Enums\Gender;
use App\Enums\User\MaritalStatus;
use App\Enums\User\Occupation;
use App\Enums\User\WageType;
use App\Filament\Admin\Resources\Users\Schemas\UserDetailsForm;
use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\EditRecord;

class EditUserDetails extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return __('filament-panels::resources/pages/edit-record.title', [
            'label' => __(':user Details', ['user' => $this->getRecord()->name]),
        ]);
    }

    public static function getNavigationLabel(): string
    {
        return __('Details');
    }

    public function getBreadcrumb(): string
    {
        return __('Details');
    }

    public function form(Schema $schema): Schema
    {
        return UserDetailsForm::configure($schema);
    }
}
