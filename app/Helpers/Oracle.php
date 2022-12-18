<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Oracle {

    /** @var ?Client $client - A Guzzle client to be used for requests */
    public static ?Client $client = null;

    /**
     * Get (and create if non-extant) the Guzzle client for making requests
     *
     * @param bool $reinitialize
     * @return Client
     */
    public static function getClient(?bool $reinitialize = false): Client
    {
        if (is_null(static::$client) || $reinitialize) {
            static::$client = new Client([
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'token' => config('services.oracle.api_key'),
                ],
                'base_uri' => config('services.oracle.api_url'),
            ]);
        }
        return static::$client;
    }

    /**
     * Create a new contract for an NFT submission
     * @param int    $nftAsaId
     * @param string $submitterAddress
     * @return object|null
     * @throws GuzzleException
     */
    public static function createSubmitContract(int $nftAsaId, string $submitterAddress): ?object
    {
        $res = static::getClient()->post("create-contract", [
            'json' => [
                'nft_asset_id' => $nftAsaId,
                'submitter_address' => $submitterAddress,
            ],
        ]);
        $content = json_decode($res->getBody()->getContents());
        return $content ?? null;
    }

    /**
     * "get" client with $reinitialize = true
     *
     * @return void
     */
    public static function reinitialize(): void
    {
        static::getClient(true);
    }
}
