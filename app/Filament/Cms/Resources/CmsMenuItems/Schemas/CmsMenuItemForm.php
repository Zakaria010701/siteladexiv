<?php

namespace App\Filament\Cms\Resources\CmsMenuItems\Schemas;

use App\Enums\Cms\CmsMenuItemType;
use App\Models\CmsMenuItem;
use App\Models\CmsPage;
use App\Models\HeaderContact;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class CmsMenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('type')
                    ->options(CmsMenuItemType::class)
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Select::make('parent_id')
                    ->visibleJs(<<<'JS'
                        $get('type') != 'dropdown' && $get('type') != 'icon'
                    JS)
                    ->relationship(
                        name:'parent',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query, ?CmsMenuItem $record) => $query->when($record, fn (Builder $query) => $query->where('id', '!=', $record->id))
                            ->where('type', 'dropdown')
                            ->whereNull('parent_id')
                    )
                    ->default(null),
                TextInput::make('url')
                    ->visibleJs(<<<'JS'
                        $get('type') == 'link' || $get('type') == 'icon'
                    JS)
                    ->default(null),
                Select::make('page')
                    ->visibleJs(<<<'JS'
                        $get('type') == 'page'
                    JS)
                    ->searchable()
                    ->formatStateUsing(fn (?CmsMenuItem $record) => $record?->reference_id)
                    ->options(fn (?CmsMenuItem $record) => CmsPage::query()
                        ->pluck('title', 'id')
                        ->all()),
                Textarea::make('icon')
                    ->visibleJs(<<<'JS'
                        $get('type') == 'icon'
                    JS)
                    ->label('Icon SVG Code')
                    ->placeholder('Paste your SVG icon code here')
                    ->rows(3),
                Select::make('header_contact')
                    ->visibleJs(<<<'JS'
                        $get('type') == 'header'
                    JS)
                    ->label('Header Contact')
                    ->searchable()
                    ->formatStateUsing(fn (?CmsMenuItem $record) => $record?->reference_id)
                    ->options(fn (?CmsMenuItem $record) => HeaderContact::query()
                        ->pluck('welcome_text', 'id')
                        ->all())
                    ->createOptionForm([
                        TextInput::make('welcome_text')
                            ->required()
                            ->default('Welcome to our website'),
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->placeholder('069 2108 9619'),
                        TextInput::make('email')
                            ->label('Email')
                            ->placeholder('Kontakt-Formular'),
                        Textarea::make('address')
                            ->label('Address')
                            ->placeholder('Konrad 15, Frankfurt Friedrichstraße 57, Wiesbaden')
                            ->rows(3),
                        TextInput::make('facebook_url')
                            ->label('Facebook URL')
                            ->placeholder('https://facebook.com/...'),
                        TextInput::make('instagram_url')
                            ->label('Instagram URL')
                            ->placeholder('https://instagram.com/...'),
                        TextInput::make('tiktok_url')
                            ->label('TikTok URL')
                            ->placeholder('https://tiktok.com/...'),
                        TextInput::make('german_flag_icon')
                            ->label('German Flag Icon')
                            ->placeholder('SVG code or icon class'),
                        TextInput::make('english_flag_icon')
                            ->label('English Flag Icon')
                            ->placeholder('SVG code or icon class'),
                        TextInput::make('position')
                            ->numeric()
                            ->default(0),
                    ])
                    ->createOptionUsing(function (array $data) {
                        return HeaderContact::create($data);
                    }),
                TextInput::make('position')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
