<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFavoriteRequest;
use App\Models\Favorite;
use App\Models\User;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $id = auth()->user()->id;
        $user = User::find($id);

        $data = $user->favorite_products()->get();
            // ->map(function ($product) { 
            //    return $product->makeHidden(['pivot']);
            // });

        return response()->json([
            'data' => $data,
            'message' => 'succeed'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFavoriteRequest $request)
    {
        $data = $request->validated();
        $userid = auth()->user()->id;
        
        if (Favorite::where('user_id', $userid)
            ->where('product_id', $data['product_id'])->first() != null) { 
                return response()->json([
                    'message' => 'product already exists'
                ], 400);
            }

        Favorite::create([
            'user_id' => $userid,
            'product_id' => $data['product_id']
        ]);

        return response()->json([
            'data' => $data, 
            'message' => 'created successfully'
        ]);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $fav = Favorite::find($id); 
        
        if (!$fav) { 
            return response()->json([   
                'message' => 'not found'
            ], 404);
        }

        if ($fav['user_id'] != auth()->user()->id){ 
            return response()->json([
                'message' => 'not allowed'
            ], 400);
        }

        $fav->delete( );

        return response()->json([
            'message' => 'deleted successfully'
        ]);
    }
}
