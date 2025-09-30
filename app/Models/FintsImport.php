<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\Transactions\FintsImportStage;
use App\Enums\Transactions\FintsImportStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $bank_id
 * @property FintsImportStatus $status
 * @property FintsImportStage $stage
 * @property string $bank_name
 * @property string|null $bank_url
 * @property string|null $bank_port
 * @property string|null $bank_code
 * @property string|null $username
 * @property string|null $password
 * @property string|null $bank_2fa
 * @property string|null $bank_2fa_device
 * @property string|null $fints_account
 * @property string|null $from_date
 * @property string|null $to_date
 * @property string|null $persisted_action
 * @property string|null $persisted_fints
 * @property array<array-key, mixed>|null $meta
 * @method static Builder<static>|FintsImport newModelQuery()
 * @method static Builder<static>|FintsImport newQuery()
 * @method static Builder<static>|FintsImport query()
 * @method static Builder<static>|FintsImport whereBank2fa($value)
 * @method static Builder<static>|FintsImport whereBank2faDevice($value)
 * @method static Builder<static>|FintsImport whereBankCode($value)
 * @method static Builder<static>|FintsImport whereBankId($value)
 * @method static Builder<static>|FintsImport whereBankName($value)
 * @method static Builder<static>|FintsImport whereBankPort($value)
 * @method static Builder<static>|FintsImport whereBankUrl($value)
 * @method static Builder<static>|FintsImport whereCreatedAt($value)
 * @method static Builder<static>|FintsImport whereFintsAccount($value)
 * @method static Builder<static>|FintsImport whereFromDate($value)
 * @method static Builder<static>|FintsImport whereId($value)
 * @method static Builder<static>|FintsImport whereMeta($value)
 * @method static Builder<static>|FintsImport wherePassword($value)
 * @method static Builder<static>|FintsImport wherePersistedAction($value)
 * @method static Builder<static>|FintsImport wherePersistedFints($value)
 * @method static Builder<static>|FintsImport whereStage($value)
 * @method static Builder<static>|FintsImport whereStatus($value)
 * @method static Builder<static>|FintsImport whereToDate($value)
 * @method static Builder<static>|FintsImport whereUpdatedAt($value)
 * @method static Builder<static>|FintsImport whereUsername($value)
 * @mixin \Eloquent
 */
class FintsImport extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'to_date' => 'date',
        'from_date' => 'date',
        'stage' => FintsImportStage::class,
        'status' => FintsImportStatus::class,
        'meta' => 'array',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
