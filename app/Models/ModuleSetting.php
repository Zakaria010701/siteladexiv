<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ModuleSetting
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $name
 * @property bool $active
 *
 * @method static Builder|ModuleSetting newModelQuery()
 * @method static Builder|ModuleSetting newQuery()
 * @method static Builder|ModuleSetting query()
 * @method static Builder|ModuleSetting whereActive($value)
 * @method static Builder|ModuleSetting whereCreatedAt($value)
 * @method static Builder|ModuleSetting whereId($value)
 * @method static Builder|ModuleSetting whereName($value)
 * @method static Builder|ModuleSetting whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ModuleSetting extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean',
    ];
}
