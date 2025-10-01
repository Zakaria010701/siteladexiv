<?php

namespace App\Filament\Cms\Resources\CmsMenuItems\Schemas;

use App\Enums\Cms\CmsMenuItemType;
use App\Models\CmsMenuItem;
use App\Models\CmsPage;
use App\Models\HeaderContact;
use Filament\Forms\Components\FileUpload;
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
                        $get('type') != 'dropdown' && $get('type') != 'icon' && $get('type') != 'button'
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
                        $get('type') == 'link' || $get('type') == 'icon' || $get('type') == 'button'
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
                FileUpload::make('icon')
                    ->visibleJs(<<<'JS'
                        $get('type') == 'icon'
                    JS)
                    ->label('Icon')
                    ->disk('public')
                    ->directory('cms/menu-icons')
                    ->image()
                    ->imageEditor()
                    ->maxSize(2048)
                    ->acceptedFileTypes(['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'])
                    ->helperText('Upload an image file (PNG, JPG, SVG, etc.) or paste SVG code below')
                    ->afterStateUpdated(function ($state, callable $set) {
                        // If file is uploaded, clear SVG code input
                        if ($state) {
                            $set('icon_svg', null);
                        }
                    }),
                Textarea::make('icon_svg')
                    ->visibleJs(<<<'JS'
                        $get('type') == 'icon' && empty($get('icon'))
                    JS)
                    ->label('Or paste SVG Code')
                    ->placeholder('<svg>...</svg>')
                    ->rows(3)
                    ->helperText('Alternative: paste raw SVG code here if not uploading a file')
                    ->dehydrated(fn ($state) => !empty($state))
                    ->afterStateUpdated(function ($state, callable $set) {
                        // If SVG code is provided, clear the file upload
                        if (!empty($state)) {
                            $set('icon', null);
                        }
                    }),
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
                            ->placeholder('Roßmarkt 15, Frankfurt Friedrichstraße 57, Wiesbaden')
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
