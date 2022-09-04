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
    public function sync(Request $request, ?Nft $nft = null): JsonResponse
    {
        $newNft = is_null($nft);

        if ($newNft) {
            $nft = Nft::create([
                'asset_id' => $request['asset-id'],
                'name' => $request->params->name,
                'unit_name' => $request->params['unit-name'],
                'creator_wallet' => $request->params['creator'],
                'meta_standard' => 'TODO:meta_standard', // TODO
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
            ]);
        }

        if ($newNft || $imageChange) {
            $nft->cacheImage();
        }

        return response()->json(['success' => ':)']);
    }
}
