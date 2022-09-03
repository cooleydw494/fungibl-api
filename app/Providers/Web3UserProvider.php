<?php

namespace App\Providers;

use Exception;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ParagonIE\ConstantTime\Base64;
use Rootsoft\Algorand\Models\Accounts\Address;
use Rootsoft\Algorand\Models\Transactions\SignedTransaction;
use Rootsoft\Algorand\Models\Transactions\Types\RawPaymentTransaction;
//use Rootsoft\Algorand\Utils\Encoder;
use App\Overrides\CustomEncoder;

/**
 * Web3 auth example
 * https://github.com/deiu/eth-auth
 */
class Web3UserProvider extends EloquentUserProvider
{

    public function __construct(HasherContract $hasher, $model)
    {
        parent::__construct($hasher, $model);
    }

    public function retrieveByCredentials(array $credentials): Model|Builder|UserContract|null
    {
        if (empty($credentials) ||
            (count($credentials) === 1 &&
                str_contains(array_keys($credentials)[0], 'signed_tx'))) {
            return null;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $query = $this->newModelQuery();

        foreach ($credentials as $key => $value) {
            if (str_contains($key, 'signed_tx')) {
                continue;
            }

            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    public function validateCredentials(UserContract $user, array $credentials): bool
    {
        try {
            // Decode the signed tx
            $txn = json_decode($credentials['signed_tx']);
            $signed_tx = $txn->txn;
            $signedTxBytes = Base64::decode(str_replace(' ', '+', $signed_tx));
            $extraTxnData = [
                'sig' => Base64::decode($txn->signature->sig),
                'type' => $txn->{'tx-type'},
                'sgnr' => $txn->sender,
                'amt' => $txn->{'payment-transaction'}->amount,
                'close' => $txn->{'payment-transaction'}->{'close-amount'},
            ];

            /* @var $signedTx SignedTransaction */
            $signedTx = CustomEncoder::getInstance()->decodeMessagePack($signedTxBytes, SignedTransaction::class, $extraTxnData);

            // Verify that the message was signed by the user public address
            $txVerified = $this->verifySignedTransaction($signedTx, $user);

            // Verify the challenge
            $challengeVerified = $this->verifyChallenge($signedTx, $user);

            return $txVerified && $challengeVerified;
        } catch (Exception $ex) {
            info($ex->getMessage());
            info($ex->getTraceAsString());
            return false;
        }
    }

    protected function verifySignedTransaction(SignedTransaction $signedTx, UserContract $user): bool
    {
        $tx = $signedTx->getTransaction();
        if (!$tx instanceof RawPaymentTransaction) {
            return false;
        }

        $sender = $tx->sender->address;
        $receiver = $tx->receiver->address;

        if ($sender !== $receiver) {
            return false;
        }

        $signature = $signedTx->getSignature()->bytes();
        $message = $signedTx->getTransaction()->getEncodedTransaction();
        $address = Address::fromAlgorandAddress($user->algorand_address);
        $publicKey = $address->address;

        return sodium_crypto_sign_verify_detached($signature, $message, $publicKey);
    }

    protected function verifyChallenge(SignedTransaction $signedTx, UserContract $user): bool
    {
        info(1);
        $note = $signedTx->getTransaction()->note;
        if (is_null($note)) {
            return false;
        }
        info(2);

        return $user->nonce == utf8_decode($note);
    }
}
