<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use Illuminate\Http\JsonResponse;

class MarketingController extends Controller
{
    /**
     * @param ContactFormRequest $request
     * @return JsonResponse
     */
    public function processContactForm(ContactFormRequest $request): JsonResponse
    {
        \DB::table('contact_messages')
           ->insert($request->only(['name', 'email', 'message']));
        return response()->json(['success' => 'success']);
    }
}
