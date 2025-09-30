<?php

namespace App\Filament\Crm\Resources\Transactions\Pages;

use Fhp\Model\FlickerTan\TanRequestChallengeFlicker;
use Fhp\Model\FlickerTan\SvgRenderer;
use InvalidArgumentException;
use Fhp\Model\TanRequestChallengeImage;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use App\Enums\Transactions\FintsImportStage;
use App\Enums\Transactions\FintsImportStatus;
use App\Exceptions\FintsImportNeedsTan;
use App\Filament\Crm\Resources\Transactions\TransactionResource;
use App\Filament\Crm\Resources\Transactions\RelationManagers\TransactionsRelationManager;
use App\Models\Account;
use App\Models\Bank;
use App\Models\FintsImport;
use App\Models\Transaction;
use App\Services\FintsImportService;
use Fhp\Model\SEPAAccount;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\HtmlString;

class WatchFintsImport extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    private FintsImportService $importService;

    public array $accounts = [];

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        $this->importService = new FintsImportService($this->getRecord());

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    protected function resolveRecord(int | string $key): FintsImport
    {
        return FintsImport::findOrFail($key);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return array_merge($data, $this->handleStage());
    }

    protected function handleStage(): array
    {
        if($this->getRecord()->status->needs2FA()) {
            return $this->handle2FA();
        }

        try {
            return match($this->getRecord()->stage) {
                FintsImportStage::Login => $this->handleLogin(),
                FintsImportStage::ChooseAccount => $this->handleChooseAccount(),
                default => [],
            };
        } catch (FintsImportNeedsTan $e) {
            $this->record = $this->getRecord()->refresh();
            return $this->handle2FA();
        }

    }

    protected function handle2FA(): array
    {
        $tanMode = $this->getImportService()->getSelectedTanMode();
        $tanRequest = $this->getImportService()->getTanRequest();

        $image = null;
        if ($tanRequest->getChallengeHhdUc()) {
            try {
                $flicker = new TanRequestChallengeFlicker($tanRequest->getChallengeHhdUc());
                // save or output svg
                $flickerPattern = $flicker->getFlickerPattern();
                // other renderers can be implemented with this pattern
                $svg = new SvgRenderer($flickerPattern);
                $image =  $svg->getImage();
            } catch (InvalidArgumentException $e) {
                // was not a flicker
                $challengeImage = new TanRequestChallengeImage(
                    $tanRequest->getChallengeHhdUc()
                );
                // Save the challenge image somewhere
                // Alternative: HTML sample code
                $image = '<img src="data:' . htmlspecialchars($challengeImage->getMimeType()) . ';base64,' . base64_encode($challengeImage->getData()) . '" />';
            }
        }

        return [
            'decoupled' => $tanMode->isDecoupled(),
            'challenge' => $tanRequest->getChallenge(),
            'tanMedium' => $tanRequest->getTanMediumName(),
            'image' => $image,
        ];
    }

    protected function handleLogin(): array
    {
        $this->getImportService()->login();
        $record = $this->getRecord();
        $record->stage = FintsImportStage::ChooseAccount;
        $record->save();
        $this->record->refresh();

        return $this->handleChooseAccount();
    }

    protected function handleChooseAccount(): array
    {
        $this->accounts = collect($this->getImportService()->getAccounts())
            ->mapWithKeys(fn (SEPAAccount $account) => [$account->getAccountNumber() => $account->getIban()])
            ->toArray();

        /** @var null|FintsImport */
        $lastImport = FintsImport::where('status', FintsImportStatus::Done)->latest('to_date')->first();
        return [
            'fints_account' => $lastImport?->fints_account,
            'bank_id' => $lastImport?->bank_id,
            'from_date' => $lastImport?->to_date?->format('Y-m-d'),
            'to_date' => today()->subDay()->format('Y-m-d'),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model($this->record)
            ->components([
                Section::make(__('Handle Tan'))
                    ->visible(fn (FintsImport $record) => $record->status->needs2FA())
                    ->schema([
                        Hidden::make('decoupled'),
                        TextInput::make('challenge')
                            ->readOnly()
                            ->hidden(fn ($state) => empty($state)),
                        TextInput::make('tanMedium')
                            ->readOnly()
                            ->hidden(fn ($state) => empty($state)),
                        Hidden::make('image'),
                        Placeholder::make('challenge_image')
                            ->content(fn (Get $get) => new HtmlString($get('image')))
                            ->hidden(fn (Get $get) => empty($get('image'))),
                        TextInput::make('tan')
                            ->required()
                            ->label(__('Tan'))
                            ->placeholder(__('Enter your Tan'))
                            ->hidden(fn (Get $get) => $get('decoupled')),
                    ]),
                Section::make(__('Choose Account'))
                    ->visible(fn (FintsImport $record) => $record->stage === FintsImportStage::ChooseAccount && $record->status->isPending())
                    ->schema([
                        Select::make('fints_account')
                            ->required()
                            ->options(fn () => $this->accounts),
                        Select::make('bank_id')
                            ->required()
                            ->options(Bank::all()->mapWithKeys(fn (Bank $bank) => [$bank->id => $bank->name])),
                        DatePicker::make('from_date')
                            ->required(),
                        DatePicker::make('to_date')
                            ->required(),
                    ]),
            ]);
    }

    protected function handleRecordUpdate(FintsImport|Model $record, array $data): FintsImport
    {
        if($record->status->needs2FA()) {
            return $this->commit2FA($record, $data);
        }

        return match ($record->stage) {
            FintsImportStage::ChooseAccount => $this->commitChooseAccount($record, $data),
            FintsImportStage::Import => $this->import($record),
            default => $record,
        };
    }

    protected function commit2FA(FintsImport $record, array $data): FintsImport
    {
        if(!$data['decoupled']) {
            $this->getImportService()->submitTan($data['tan']);
        }

        $record->status = FintsImportStatus::Pending;
        $record->save();

        return $record;
    }

    protected function commitChooseAccount(FintsImport $record, array $data): FintsImport
    {
        $record->fints_account = $data['fints_account'];
        $record->bank_id = $data['bank_id'];
        $record->from_date = Carbon::parse($data['from_date']);
        $record->to_date = Carbon::parse($data['to_date']);
        $record->stage = FintsImportStage::Import;
        $record->save();

        return $this->import($record);
    }

    protected function import(FintsImport $record)
    {
        if($record->status == FintsImportStatus::Done) {
            return $record;
        }
        collect($this->getImportService()->getTransactions())
            ->reject(fn (array $transaction) => $this->checkForDublicate($transaction['hash']))
            ->map(fn (array $transaction) => $this->attachAccountToTransaction($transaction))
            ->each(fn (array $transaction) => Transaction::create($transaction));
        $this->getImportService()->close();

        $record->status = FintsImportStatus::Done;
        $record->save();
        return $record;
    }

    protected function attachAccountToTransaction(array $transaction)
    {
        $account = Account::firstOrCreate([
            'iban' => $transaction['iban'],
        ], [
            'name' => $transaction['name'],
        ]);

        $transaction['account_id'] = $account->id;
        return $transaction;
    }

    protected function checkForDublicate(string $hash)
    {
        return Transaction::where('hash', $hash)->exists();
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->operation('edit')
                    ->model($this->getModel())
                    ->statePath($this->getFormStatePath())
                    ->columns($this->hasInlineLabels() ? 1 : 2)
                    ->inlineLabel($this->hasInlineLabels()),
            ),
        ];
    }

    public function getImportService(): FintsImportService
    {
        if(isset($this->importService)) {
            return $this->importService;
        }

        $record = $this->getRecord();
        $this->importService = new FintsImportService($record);
        return $this->importService;
    }

    public function getRecord(): FintsImport
    {
        return $this->record;
    }

    protected function getAllRelationManagers(): array
    {
        return [
            TransactionsRelationManager::class,
        ];
    }

}
