<?php

namespace App\Providers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Column;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch->simple()->renderHook(PanelsRenderHook::USER_MENU_PROFILE_AFTER);
        });

        Column::configureUsing(function (Column $column) {
            $column
                ->toggleable()
                ->translateLabel();
        });

        Fieldset::configureUsing(fn (Fieldset $fieldset) => $fieldset
            ->columnSpanFull());

        Grid::configureUsing(fn (Grid $grid) => $grid
            ->columnSpanFull());

        Section::configureUsing(fn (Section $section) => $section
            ->columnSpanFull());

        Field::configureUsing(function (Field $field) {
            $field->translateLabel();
        });

        DateTimePicker::configureUsing(function (DateTimePicker $field) {
            $field->seconds(false);
        });

        Action::configureUsing(function (Action $action) {
            $action->translateLabel();
        });

        MorphToSelect::configureUsing(function (MorphToSelect $field) {
            $field->translateLabel();
        });

        CreateAction::configureUsing(function (Action $action) {
            $action->icon(Heroicon::Plus);
        });

        DeleteAction::configureUsing(function (Action $action) {
            $action->icon(Heroicon::Trash);
        });

        Toggle::configureUsing(function (Toggle $field) {
            $field->inline(false);
        });

        // Removed configurePermissionIdentifierUsing as it's not supported in Filament Shield v4

        FilamentAsset::register([
            AlpineComponent::make('filament-fullcalendar-alpine', __DIR__.'/../../resources/dist/js/components/filament-fullcalendar.js'),
            Css::make('filament-fullcalendar-styles', __DIR__.'/../../resources/dist/css/filament-fullcalendar.css'),
            Css::make('cms-styles', __DIR__.'/../../resources/css/cms-styles.css'),
        ]);
    }
}
