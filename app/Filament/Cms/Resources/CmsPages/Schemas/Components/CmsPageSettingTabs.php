<?php

namespace App\Filament\Cms\Resources\CmsPages\Schemas\Components;

use App\Enums\Cms\CmsPageStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class CmsPageSettingTabs
{
    public static function make(): Tabs
    {
        return Tabs::make()
            ->columnSpanFull()
            ->tabs([
                Tabs\Tab::make(__('General'))
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->partiallyRenderComponentsAfterStateUpdated(['slug'])
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->unique(ignoreRecord: true)
                            ->required(),
                    ]),
                Tabs\Tab::make(__('cms.page.seo'))
                    ->schema([
                        Textarea::make('description')
                            ->default(null)
                            ->columnSpanFull(),
                        Textarea::make('keywords')
                            ->default(null)
                            ->columnSpanFull(),
                    ]),
                Tabs\Tab::make(__('cms.page.visibility'))
                    ->schema([
                        Select::make('status')
                            ->options(CmsPageStatus::class)
                            ->default(CmsPageStatus::Draft)
                            ->required(),
                        DateTimePicker::make('published_at'),
                    ]),
            ]);
    }
}