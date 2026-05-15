<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MetaKeyword;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;

class MetaKeywordsController extends Controller
{
    public function show($slug)
    {
        $meta = MetaKeyword::where('slug', $slug)->first();

        if (!$meta) {
            return response()->json([
                'success' => false,
                'message' => 'Meta not found'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $meta,
        ]);
    }
}
