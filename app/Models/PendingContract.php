<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PendingContract
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PendingContract newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PendingContract newQuery()
 * @method static \Illuminate\Database\Query\Builder|PendingContract onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PendingContract query()
 * @method static \Illuminate\Database\Query\Builder|PendingContract withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PendingContract withoutTrashed()
 * @mixin \Eloquent
 * @property int $id
 * @property int $nft_asset_id
 * @property int $user_id
 * @property string $contract_info
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PendingContract whereContractInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingContract whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingContract whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingContract whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingContract whereNftAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingContract whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingContract whereUserId($value)
 */
class PendingContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
}
