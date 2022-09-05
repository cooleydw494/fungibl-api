<?php

namespace App\Http\Controllers;

use App\Models\Nft;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NftController extends Controller
{

    /**
     * Use a specific NFT's data from the front-end to sync with the DB record
     *
     * @param Request  $request
     * @param Nft|null $nft
     * @return JsonResponse
     */
    public function sync(Request $request, int $assetId): JsonResponse
    {
        $nft = Nft::find($assetId);
        $newNft = is_null($nft);

        if ($newNft) {
            $nft = Nft::create([
                'asset_id' => $request['asset-id'],
                'name' => $request->params['name'],
                'unit_name' => $request->params['unit-name'],
                'collection_name' => 'TODO:collection_name',
                'creator_wallet' => $request->params['creator'],
                'meta_standard' => 'TODO:ms', // TODO
                'metadata' => 'TODO:metadata', // TODO
                'ipfs_image_url' => $request->imageUrl,
            ]);
        }

        $imageChange = !$newNft && $nft->ipfs_image_url !== $request->imageUrl;
        $metadataChange = !$newNft && $nft->metadata !== 'TODO:metadata'; // TODO

        if ($metadataChange || $imageChange) {
            $nft->update([
                'metadata' => 'WIP',
                'ipfs_image_url' => $request->imageUrl,
                'image_cached' => ($nft->image_cached && !$imageChange),
            ]);
        }

        return response()->json(['success' => ':)', 'image_cached' => $nft->image_cached]);
    }

    public function cacheImage(Request $request, int $assetId): JsonResponse
    {
        $nft = Nft::findOrFail($assetId);
        $cached = $nft->cacheImage();
        info(json_encode($cached));
        $nft->update(['image_cached' => (bool)$cached]);

        return response()->json(['cache_result' => $cached]);
    }
}
