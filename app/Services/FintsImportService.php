<?php
namespace App\Services;

use App\Enums\Transactions\FintsImportStatus;
use App\Enums\Transactions\TransactionStatus;
use App\Enums\Transactions\TransactionType;
use App\Exceptions\FintsImportNeedsTan;
use App\Models\Account;
use App\Models\FintsImport;
use DateTime;
use Fhp\Action\GetSEPAAccounts;
use Fhp\Action\GetStatementOfAccount;
use Fhp\BaseAction;
use Fhp\CurlException;
use Fhp\FinTs;
use Fhp\Model\SEPAAccount;
use Fhp\Model\StatementOfAccount\StatementOfAccount;
use Fhp\Model\StatementOfAccount\Transaction;
use Fhp\Model\TanMode;
use Fhp\Model\TanRequest;
use Fhp\Options\Credentials;
use Fhp\Options\FinTsOptions;
use Fhp\Protocol\DialogInitialization;
use Fhp\Protocol\ServerException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class FintsImportService
{
    private ?FinTsOptions $options;
    private ?Credentials $credentials;

    private ?FinTs $fints;

    public function __construct(private FintsImport $import)
    {

    }

    public function getOptions(): FinTsOptions
    {
        if(isset($this->options)) {
            return $this->options;
        }

        $this->options = new FinTsOptions();
        $this->options->url = $this->import->bank_url;
        $this->options->bankCode = $this->import->bank_code;
        $this->options->productName = config('fints.registration_code');
        $this->options->productVersion = config('fints.version');
        return $this->options;
    }

    public function getCredentials(): Credentials
    {
        if(isset($this->credentials)) {
            return $this->credentials;
        }

        $this->credentials = Credentials::create($this->import->username, Crypt::decryptString($this->import->password));
        return $this->credentials;
    }

    public function getFints(): FinTs
    {
        if(isset($this->fints)) {
            return $this->fints;
        }

        if(isset($this->import->persisted_fints)) {
            $this->fints = Fints::new($this->getOptions(), $this->getCredentials(), $this->import->persisted_fints);
            return $this->fints;
        }

        $this->fints = Fints::new($this->getOptions(), $this->getCredentials());
        $this->fints->selectTanMode(intval($this->import->bank_2fa));
        return $this->fints;
    }

    public function getAction(): ?BaseAction
    {
        if(isset($this->import->persisted_action)) {
            return unserialize($this->import->persisted_action);
        }

        return null;
    }

    public function persist(?BaseAction $action = null): void
    {
        $this->import->persisted_fints = $this->getFints()->persist();
        if(!is_null($action)) {
            $this->import->persisted_action = serialize($action);
        }
        $this->import->save();
    }

    public function handleTan(BaseAction $action)
    {
        if(!$action->needsTan()) {
            return;
        }

        $this->import->status = FintsImportStatus::Needs2FA;
        $this->persist($action);
        throw new FintsImportNeedsTan();
    }

    public function getTanRequest() : ? TanRequest
    {
        return $this->getAction()?->getTanRequest();
    }

    /**
     * @throws CurlException
     * @throws ServerException
     */
    public function getSelectedTanMode(): ?TanMode
    {
        return $this->getFints()->getSelectedTanMode();
    }

    /**
     * @throws CurlException
     * @throws ServerException
     */
    public function submitTan($tan): void
    {
        $this->getFints()->submitTan($this->getAction(), $tan);
    }

    /**
     * @throws CurlException
     * @throws FintsImportNeedsTan
     * @throws ServerException
     */
    public function login(): void
    {
        $action = $this->getAction();
        if(!($action instanceof DialogInitialization)) {
            $action = $this->getFints()->login();
        }
        $this->handleTan($action);
        $this->persist();
    }

    /**
     * @throws CurlException
     * @throws FintsImportNeedsTan
     * @throws ServerException
     */
    public function getAccounts(): array
    {
        $action = $this->getAction();
        if(!($action instanceof GetSEPAAccounts)) {
            $action = GetSEPAAccounts::create();
        }

        $this->getFints()->execute($action);
        $this->handleTan($action);
        $this->persist();
        return $action->getAccounts();
    }

    /**
     * @throws CurlException
     * @throws FintsImportNeedsTan
     * @throws ServerException
     */
    public function getTransactions(): array
    {
        if(is_null($this->import->fints_account)) {
            return [];
        }
        if(is_null($this->import->to_date)) {
            return [];
        }
        if(is_null($this->import->from_date)) {
            return [];
        }

        $fints = $this->getFints();

        /** @var ?SEPAAccount $account */
        $account = collect($this->getAccounts())
            ->filter(fn (SEPAAccount $account) => $account->getAccountNumber() === $this->import->fints_account)
            ->first();

        if($account === null) {
            return [];
        }

        $statement = $this->getStatementOfAccount($account);

        $transactions = [];
        foreach ($statement->getStatements() as $statement) {
            foreach ($statement->getTransactions() as $transaction) {
                $mappedTransaction = $this->mapTransaction($transaction);
                if($mappedTransaction) {
                    $transactions[] = $mappedTransaction;
                }
            }
        }

        return $transactions;
    }

    /**
     * @throws CurlException
     * @throws FintsImportNeedsTan
     * @throws ServerException
     */
    public function getStatementOfAccount(SEPAAccount $account): StatementOfAccount
    {
        $action = $this->getAction();
        if(!($action instanceof GetStatementOfAccount)) {
            $from = Carbon::parse($this->import->from_date);
            $to = Carbon::parse($this->import->to_date);
            $action = GetStatementOfAccount::create($account, $from, $to);
        }

        $this->fints->execute($action);
        $this->handleTan($action);
        $this->persist();

        return $action->getStatement();
    }

    public function close(): void
    {
        $this->fints->close();
    }

    private function mapTransaction(Transaction $transaction): ?array
    {
        $type = ($transaction->getCreditDebit() !== Transaction::CD_CREDIT) ? TransactionType::Withdrawal : TransactionType::Deposit;

        $hash = Hash::make(json_encode([
            $transaction->getValutaDate()->format('Y-m-d H:i'),
            $transaction->getMainDescription(),
            $transaction->getAmount()
        ]));

        return [
            'name' => $transaction->getName(),
            'iban' => $transaction->getAccountNumber(),
            'date' => $transaction->getValutaDate(),
            'description' => $transaction->getMainDescription(),
            'amount' => $transaction->getAmount(),
            'type' => $type,
            'bank_id' => $this->import->bank_id,
            'status' => TransactionStatus::Open,
            'hash' => $hash,
            'fints_import_id' => $this->import->id,
            'meta' => [
                'name' => $transaction->getName(),
                'account_number' => $transaction->getAccountNumber(),
            ]
        ];
    }

}
