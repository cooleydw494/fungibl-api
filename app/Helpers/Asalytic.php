<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Asalytic {

    /** @var Client $client - A Guzzle client to be used for requests */
    public static Client $client;

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
                    'base_uri' => config('asalytic.api_url'),
                    'token' => config('asalytic.api_key'),
                ],
            ]);
        }
        return static::$client;
    }

    /**
     * Get the Asalytic estimated price of an ASA
     *
     * @param int $asaId
     * @return int|null
     * @throws GuzzleException
     */
    public static function estimatedPrice(int $asaId): ?int
    {
        $res = static::getClient()->get("asas/$asaId/estimatedPrice");
        $content = json_decode($res->getBody()->getContents());
        return $content->estimated_price ?? null;
    }

    /**
     * Get all sales for pieces of a collection that match ANY of the traits.
     * ANY of the traits, as in Body->Gold OR Body->Hypno
     *
     * Ex $traits)  singular values - ['Body' => 'Gold', ...]
     * Ex $traits)  multiple values - ['Body' => ['Gold', 'Hypno'], ...]
     * Note: you can mix and match singular and multiple value trait types
     *
     * In the final $url built in this function $traits/values must correspond
     * in order     traitTypes=1,2&traitValues=1,2
     * Ex $url)     traitTypes=Body,Body&traitValues=Gold,Hypno
     *
     * @param string $collectionName
     * @param array  $traits
     * @return mixed
     * @throws GuzzleException
     */
    public static function salesFor(string $collectionName, array $traits)
    {
        $url = "collections/$collectionName/traitSales?";
        $traitTypes = [];
        $traitValues = [];
        foreach ($traits as $type => $values) {
            if (is_string($values)) {
                $traitTypes[] = $type;
                $traitValues[] = $values; // if string is actually singular val
                continue;
            }
            foreach ($values as $value) {
                $traitTypes[] = $type;
                $traitValues[] = $value;
            }
        }
        $traitTypes = implode(',', $traitTypes);
        $traitValues = implode(',', $traitValues);

        $url .= "traitTypes=$traitTypes&traitValues=$traitValues";
        $res = static::getClient()->get($url);
        $content = json_decode($res->getBody()->getContents());
        return $content;
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
