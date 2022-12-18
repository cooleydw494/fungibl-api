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
 */
class PendingContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
}
