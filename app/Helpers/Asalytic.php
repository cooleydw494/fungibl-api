<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Asalytic {

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
                    'token' => config('services.asalytic.api_key'),
                ],
                'base_uri' => config('services.asalytic.api_url'),
            ]);
        }
        return static::$client;
    }

    /**
     * Get the Asalytic estimated price of an ASA
     *
     * returns object {
     *      "asa_id": 639283943,
     *      "collection_name": "2 tinyhorse",
     *      "trait_estimate": 653.6,
     *      "price_estimate": 653.6,
     *      "collection_floor": 230
     * }
     *
     * @param int $asaId
     * @return object|null
     * @throws GuzzleException
     */
    public static function estimatedPrice(int $asaId): ?object
    {
        $res = static::getClient()->get("asa/$asaId/priceEstimate");
        $content = json_decode($res->getBody()->getContents());
        return $content ?? null;
    }

    /**
     * Get the estimated price & floor price for the given collection
     *
     * returns object {
     *      "collection_name": "M.N.G.O",
     *      "estimated_price": 1140,
     *      "floor_price": 899
     * }
     *
     * @param string $collectionName
     * @return mixed
     * @throws GuzzleException
     */
    public static function collectionEstimateData(string $collectionName): object
    {
        $res = static::getClient()
                     ->get("collection/$collectionName/priceEstimate");
        $content = json_decode($res->getBody()->getContents());
        return $content;
    }

    /**
     * Get all sales for pieces of a collection that match ANY of the traits.
     * ANY of the traits, as in Body->Gold OR Body->Hypno. Array optionally null.
     *
     * Ex $traits)  singular values - ['Body' => 'Gold', ...]
     * Ex $traits)  multiple values - ['Body' => ['Gold', 'Hypno'], ...]
     * Note: you can mix and match singular and multiple value trait types
     *
     * In the final $url built in this function $traits/values must correspond
     * in order     traitTypes=1,2&traitValues=1,2
     * Ex $url)     traitTypes=Body,Body&traitValues=Gold,Hypno
     *
     * returns object with "sales" array of sales (paginated) {
     *      "seller": "XXXX",
     *      "buyer": "XXXX",
     *      "price": 8750,
     *      "time": 1662429513,
     *      "asa_id": 358545766,
     *      "sale_platform": "Atomixwap",
     *      "group_id": "XXXX/XXXX/XXXX=",
     *      "collection_name": "M.N.G.O",
     * }
     * but we'll actually return an array of the first page
     * (25 results) idk if we'll ever need all of them
     *
     * @param string $collectionName
     * @param array|null  $traits
     * @return array
     * @throws GuzzleException
     */
    public static function salesFor(string $collectionName, ?array $traits = null): array
    {
        $url = "collection/$collectionName/sales?sortType=timeHighToLow";
        if (! is_null($traits)) {
            $traitTypesAndValues = static::parseTraitTypesAndValues($traits);
            $url .= "&$traitTypesAndValues";
        }
        $res = static::getClient()->get($url);
        $content = json_decode($res->getBody()->getContents());
        return $content->sales ?? [];
    }

    /**
     * Get all listings for pieces of a collection that match ANY of the traits.
     * ANY of the traits, as in Body->Gold OR Body->Hypno. Array optionally null.
     *
     * Ex $traits)  singular values - ['Body' => 'Gold', ...]
     * Ex $traits)  multiple values - ['Body' => ['Gold', 'Hypno'], ...]
     * Note: you can mix and match singular and multiple value trait types
     *
     * In the final $url built in this function $traits/values must correspond
     * in order     traitTypes=1,2&traitValues=1,2
     * Ex $url)     traitTypes=Body,Body&traitValues=Gold,Hypno
     *
     * returns object with "sales" array of listings (paginated) {
     *      "asa": {
     *          "creator": "XXXX",
     *          "asa_id": 359286968,
     *          "name": "M.N.G.O #2668",
     *          "supply": 1,
     *          "ipfs_image": "https://asalytic.sfo3.digitaloceanspaces.com/359286968.WEBP",
     *          "cached_image": "https://asalytic.mypinata.cloud/ipfs/QmaReSnDqkFXWm4CHdhcGu6wHaSw215gxBBA4QBkcXe8jB",
     *          "collection_name": "M.N.G.O",
     *          "collection_id": null,
     *          "arc69": {},
     *      ,}
     *      "platform": "Rand Gallery",
     *      "price": 3750,
     *      "time": 1658625361000,
     *      "seller": "XXXX",
     *      "slug": "https://www.randgallery.com/algo-collection/?address=359286968"
     * }
     *
     * @param string $collectionName
     * @param array|null  $traits
     * @return array
     * @throws GuzzleException
     */
    public static function listingsFor(string $collectionName, ?array $traits = null): array
    {
        $url = "collection/$collectionName/listings?sortType=timeHighToLow";
        if (! is_null($traits)) {
            $traitTypesAndValues = static::parseTraitTypesAndValues($traits);
            $url .= "&$traitTypesAndValues";
        }
        $res = static::getClient()->get($url);
        $content = json_decode($res->getBody()->getContents());
        return $content->listings ?? [];
    }

    /**
     * Get query string for traitTypes and traitValues
     *
     * @param array $traits
     * @return string
     */
    public static function parseTraitTypesAndValues(array $traits): string
    {
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

        return "traitTypes=$traitTypes&traitValues=$traitValues";
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
