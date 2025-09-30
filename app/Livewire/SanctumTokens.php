<?php

namespace App\Livewire;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Models\User;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Laravel\Sanctum\Sanctum;
use Livewire\Component;

class SanctumTokens extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public User $user;

    public ?string $plainTextToken;

    public function mount()
    {
        $this->user = Filament::getCurrentOrDefaultPanel()->auth()->user();
    }

    public function render()
    {
        return view('livewire.sanctum-tokens');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(app(Sanctum::$personalAccessTokenModel)->where([
                ['tokenable_id', '=', $this->user->id],
                ['tokenable_type', '=', get_class($this->user)],
            ]))
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->color(fn ($record) => now()->gt($record->expires_at) ? 'danger' : null)
                    ->date()
                    ->sortable(),
                TextColumn::make('abilities')
                    ->badge()
                    ->getStateUsing(fn ($record) => count($record->abilities)),
            ])
            ->filters([
                // ...
            ])
            ->heading(__('Tokens'))
            ->description(fn () => $this->plainTextToken ?? '')
            ->headerActions([
                Action::make('createToken')
                    ->label(__('Create'))
                    ->schema($this->getSanctumFormSchema())
                    ->action(function ($data) {
                        $this->plainTextToken = $this->user->createToken($data['token_name'], array_values($data['abilities']), $data['expires_at'] ? Carbon::createFromFormat('Y-m-d', $data['expires_at']) : null)->plainTextToken;
                        Notification::make()
                            ->success()
                            ->title(__('Created Token'))
                            ->body($this->plainTextToken)
                            ->send();
                    }),
            ])
            ->recordActions([
                EditAction::make('edit')
                    ->iconButton()
                    ->schema($this->getSanctumFormSchema(edit: true)),
                DeleteAction::make()
                    ->iconButton(),
            ])
            ->toolbarActions([
                // ...
            ]);
    }

    protected function getSanctumFormSchema(bool $edit = false): array
    {
        return [
            TextInput::make('token_name')
                ->required()
                ->hidden($edit),
            CheckboxList::make('abilities')
                ->options([
                    '*' => __('All'),
                    'branches' => __('Branches'),
                    'categories' => __('Categories'),
                    'services' => __('Services'),
                ])
                ->columns(3)
                ->required(),
            DatePicker::make('expires_at'),
        ];
    }
}
