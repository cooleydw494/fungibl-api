<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Nft
 *
 * @property int $asset_id
 * @property string $name
 * @property string $unit_name
 * @property string $collection_name
 * @property string $creator_wallet
 * @property string $meta_standard
 * @property string|null $metadata
 * @property string $ipfs_image_url
 * @property int|null $image_cached
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Nft newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Nft newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Nft query()
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereCollectionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereCreatorWallet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereImageCached($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereIpfsImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereMetaStandard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereUnitName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereUpdatedAt($value)
 */
	class Nft extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PoolNft
 *
 * @property int $asset_id
 * @property string $name
 * @property string $unit_name
 * @property string $collection_name
 * @property string $creator_wallet
 * @property string $meta_standard
 * @property string|null $metadata
 * @property string $ipfs_image_url
 * @property int|null $image_cached
 * @property int $in_pool
 * @property int $submit_est_algo
 * @property string $submit_algorand_address
 * @property int $submit_iteration
 * @property int|null $pull_est_algo
 * @property string|null $pulled_at
 * @property string|null $pull_algorand_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft query()
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereCollectionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereCreatorWallet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereImageCached($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereInPool($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereIpfsImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereMetaStandard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft wherePullAlgorandAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft wherePullEstAlgo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft wherePulledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereSubmitAlgorandAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereSubmitEstAlgo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereSubmitIteration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereUnitName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereUpdatedAt($value)
 */
	class PoolNft extends \Eloquent {}
}

namespace App\Models{
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
 */
	class User extends \Eloquent implements \Tymon\JWTAuth\Contracts\JWTSubject {}
}

