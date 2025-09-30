<?php

namespace App\Filament\Admin\Clusters\Notification\Resources\NotificationTemplates;

use Filament\Schemas\Schema;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Clusters\Notification\Resources\NotificationTemplates\Pages\ListNotificationTemplates;
use App\Filament\Admin\Clusters\Notification\Resources\NotificationTemplates\Pages\CreateNotificationTemplate;
use App\Filament\Admin\Clusters\Notification\Resources\NotificationTemplates\Pages\EditNotificationTemplate;
use App\Enums\Notifications\NotificationType;
use App\Filament\Admin\Clusters\Notification\NotificationCluster;
use App\Filament\Admin\Clusters\Notification\Resources\NotificationTemplateResource\Pages;
use App\Models\NotificationTemplate;
use App\Support\TemplateSupport;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NotificationTemplateResource extends Resource
{
    protected static ?string $model = NotificationTemplate::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = NotificationCluster::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                CheckboxList::make('branches')
                    ->required()
                    ->bulkToggleable()
                    ->relationship(titleAttribute: 'name'),
                Select::make('type')
                    ->options(NotificationType::class)
                    ->live()
                    ->required()
                    ->unique(ignoreRecord: true),
                Toggle::make('is_enabled')
                    ->inline(false)
                    ->default(true),
                TextInput::make('subject')
                    ->columnSpanFull()
                    ->required()
                    ->maxLength(255),
                Repeater::make('reminder')
                    ->label(__('Reminder'))
                    ->hint(__('The Reminder will be sent at every specified number of days before the appointment'))
                    ->columnSpanFull()
                    ->relationship('appointmentReminderSettings')
                    ->visible(fn (Get $get) => $get('type') == NotificationType::AppointmentReminder->value)
                    ->simple(
                        TextInput::make('days_before')
                            ->required()
                            ->suffix(__('Days'))
                    ),
                Toggle::make('enable_mail')
                    ->default(true)
                    ->live(),
                RichEditor::make('content')
                    ->visible(fn (Get $get) => $get('enable_mail'))
                    ->extraInputAttributes(['style' => 'min-height: 24rem;'])
                    ->mergeTags(TemplateSupport::make()->getPlaceholderNames())
                    ->collapseBlocksPanel()
                    ->columnSpanFull()
                    ->json()
                    ->required(fn (Get $get) => $get('enable_mail')),
                Toggle::make('enable_sms')
                    ->default(false)
                    ->live(),
                RichEditor::make('sms_content')
                    ->visible(fn (Get $get) => $get('enable_sms'))
                    ->extraInputAttributes(['style' => 'min-height: 24rem;'])
                    ->mergeTags(TemplateSupport::make()->getPlaceholderNames())
                    ->collapseBlocksPanel()
                    ->columnSpanFull()
                    ->json()
                    ->required(fn (Get $get) => $get('enable_sms')),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('subject'),
                ViewEntry::make('content')
                    ->columnSpanFull()
                    ->view('filament.infolists.preview'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type'),
                TextColumn::make('subject')
                    ->searchable(),
                TextColumn::make('branches.name'),
                IconColumn::make('is_enabled')
                    ->sortable()
                    ->boolean(),
                IconColumn::make('enable_mail')
                    ->sortable()
                    ->boolean(),
                IconColumn::make('enable_sms')
                    ->sortable()
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(NotificationType::class),
                SelectFilter::make('branches')
                    ->relationship('branches', 'name')
                    ->preload()
                    ->multiple(),
                TernaryFilter::make('is_enabled'),
                TernaryFilter::make('enable_mail'),
                TernaryFilter::make('enable_sms'),
            ])
            ->recordActions([
                EditAction::make(),
                ActionGroup::make([
                    ViewAction::make(),
                    DeleteAction::make(),
                ]),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotificationTemplates::route('/'),
            'create' => CreateNotificationTemplate::route('/create'),
            'edit' => EditNotificationTemplate::route('/{record}/edit'),
        ];
    }
}
