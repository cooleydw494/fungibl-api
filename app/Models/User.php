<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $algorand_address
 * @property string|null $username
 * @property string|null $nfd
 * @property int|null $favorite_nft
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $nonce
 * @property int $submission_count
 * @property int $submission_count_mtd
 * @property int $submission_count_ytd
 * @property int $pull_count
 * @property int $pull_count_mtd
 * @property int $pull_count_ytd
 * @property int $submission_est_algo
 * @property int $submission_est_algo_mtd
 * @property int $submission_est_algo_ytd
 * @property int $pull_est_algo
 * @property int $pull_est_algo_mtd
 * @property int $pull_est_algo_ytd
 * @property int $fun_rewarded
 * @property int $fun_rewarded_mtd
 * @property int $fun_rewarded_ytd
 * @property int $fun_spent
 * @property int $fun_spent_mtd
 * @property int $fun_spent_ytd
 * @property int $fees_paid_algo
 * @property int $fees_paid_algo_mtd
 * @property int $fees_paid_algo_ytd
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAlgorandAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFavoriteNft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFeesPaidAlgo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFeesPaidAlgoMtd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFeesPaidAlgoYtd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFunRewarded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFunRewardedMtd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFunRewardedYtd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFunSpent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFunSpentMtd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFunSpentYtd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNfd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNonce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePullCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePullCountMtd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePullCountYtd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePullEstAlgo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePullEstAlgoMtd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePullEstAlgoYtd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSubmissionCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSubmissionCountMtd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSubmissionCountYtd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSubmissionEstAlgo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSubmissionEstAlgoMtd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSubmissionEstAlgoYtd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
