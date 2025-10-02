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
use Illuminate\Support\Facades\Log;

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
                Select::make('reference_id')
                    ->label('Page')
                    ->visible(function (callable $get) {
                        $type = $get('type');
                        $typeValue = is_object($type) ? $type->value : $type;
                        Log::info('Menu item type selected: ' . $typeValue . ', showing page field: ' . ($typeValue === 'page' ? 'YES' : 'NO'));
                        return $typeValue === 'page';
                    })
                    ->searchable()
                    ->options(function (?CmsMenuItem $record) {
                        // Force load from CmsPage table specifically
                        $pages = CmsPage::query()
                            ->select('id', 'title', 'slug')
                            ->orderBy('title')
                            ->get();

                        $options = [];
                        foreach ($pages as $page) {
                            $options[$page->id] = $page->title . ' (ID: ' . $page->id . ', Slug: ' . $page->slug . ')';
                        }

                        Log::info('CMS PAGES loaded for menu selection: ' . count($options));
                        if (!empty($options)) {
                            Log::info('First few CMS page options: ' . json_encode(array_slice($options, 0, 3, true)));
                        }

                        return $options ?: ['' => 'No CMS pages found - please create some pages first'];
                    })
                    ->default(fn (?CmsMenuItem $record) => $record?->reference_id)
                    ->dehydrateStateUsing(function ($state, ?CmsMenuItem $record) {
                        if ($record && $state) {
                            $record->reference_type = 'App\Models\CmsPage';
                            $record->reference_id = $state;
                        }
                        return $state;
                    })
                    ->helperText('Select a CMS page to link this menu item to')
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set) {
                        Log::info('Page selected for menu item: ' . $state);
                    }),
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
                Select::make('header_contact_id')
                    ->label('Header Contact')
                    ->visibleJs(<<<'JS'
                        $get('type') == 'header'
                    JS)
                    ->searchable()
                    ->options(fn (?CmsMenuItem $record) => HeaderContact::query()
                        ->pluck('welcome_text', 'id')
                        ->all())
                    ->default(fn (?CmsMenuItem $record) => $record?->reference_id)
                    ->dehydrateStateUsing(function ($state, ?CmsMenuItem $record) {
                        if ($record && $state) {
                            $record->reference_type = 'App\Models\HeaderContact';
                            $record->reference_id = $state;
                        }
                        return $state;
                    })
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
