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
     * @return JsonResponse
     */
    public function sync(Request $request): JsonResponse
    {
        $needsCaching = [];
        foreach ($request->nfts as $nftData) {
            $nft = Nft::syncFromFrontend($nftData);
            $nft->image_cached ?: $needsCaching[] = $nft->asset_id;
        }
        return response()->json(['success' => ':)', 'needs_caching' => $needsCaching]);
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
