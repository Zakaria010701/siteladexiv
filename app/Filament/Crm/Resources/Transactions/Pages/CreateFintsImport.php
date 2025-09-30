<?php

namespace App\Filament\Crm\Resources\Transactions\Pages;

use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Set;
use App\Enums\Transactions\FintsImportStage;
use App\Enums\Transactions\FintsImportStatus;
use App\Enums\Transactions\TwoFactorMethod;
use App\Filament\Crm\Resources\Transactions\TransactionResource;
use App\Models\FintsCredential;
use App\Models\FintsImport;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Svg\Tag\Text;

class CreateFintsImport extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('fints_credential')
                ->options(FintsCredential::all()->mapWithKeys(fn (FintsCredential $record) => [$record->id => $record->bank_name]))
                ->suffixAction(
                    Action::make('select')
                        ->icon('heroicon-o-check')
                        ->action(function (Set $set, $state) {
                            $credential = FintsCredential::findOrFail($state);
                            $set('bank_name', $credential->bank_name);
                            $set('bank_url', $credential->bank_url);
                            $set('bank_code', $credential->bank_code);
                            $set('username', $credential->username);
                            $set('password', Crypt::decryptString($credential->password));
                            $set('banke_2fa', $credential->bank_2fa);
                        })
                ),
            TextInput::make('bank_name')
                ->required(),
            TextInput::make('bank_url')
                ->required()
                ->url(),
            TextInput::make('bank_code')
                ->required(),
            TextInput::make('username')
                ->required(),
            TextInput::make('password')
                ->required()
                ->password()
                ->revealable(),
            Select::make('bank_2fa')
                ->options(TwoFactorMethod::class),
        ]);
    }

    /**
     * @return array<int|string, string|\Filament\Schemas\Schema>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->operation('create')
                    ->model($this->getModel())
                    ->statePath($this->getFormStatePath())
                    ->columns($this->hasInlineLabels() ? 1 : 2)
                    ->inlineLabel($this->hasInlineLabels()),
            ),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Crypt::encryptString($data['password']);
        $data['status'] = FintsImportStatus::Pending;
        $data['stage'] = FintsImportStage::Login;
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = new FintsImport($data);

        $record->save();

        FintsCredential::updateOrCreate([
            'bank_name' => $data['bank_name']
        ], [
            'bank_url' => $data['bank_url'],
            'bank_code' => $data['bank_code'],
            'bank_port' => $data['bank_port'] ?? '',
            'username' => $data['username'],
            'password' => $data['password'],
            'bank_2fa' => $data['bank_2fa'],
        ]);

        return $record;
    }

    public function canCreateAnother(): bool
    {
        return false;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('watch_fints_import', ['record' => $this->getRecord(), ...$this->getRedirectUrlParameters()]);
    }
}
