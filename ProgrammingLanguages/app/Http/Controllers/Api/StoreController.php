<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Models\Store;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Store::get(); 

        return response()->json([
            'data' => $data,
            'message' => 'succeed'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStoreRequest $request)
    {
        $data = $request->validated();

        $store = Store::create($data);

        return response()->json([
            'data' => $store,
            'message' => 'created successfully' 
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $store = Store::find($id);

        if (is_null($store)){ 
            return response()->json([
                'message' => "store not found"
            ], 404);
        }

        return response()->json([
            'data' => $store,
            'message' => "succeed"
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStoreRequest $request, $id)
    {
        $data = $request->validated();
        $store = Store::find($id); 

        if(!$store){ 
            return response()->json([
                'message' => "store not found"
            ], 404);
        }

        $store->update($data);

        return response()->json([
            'data' => $store,
            'message' => "updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $store = Store::find($id);

        if(!$store){ 
            return response()->json([
                'message' => "store not found"
            ], 404);
        }

        $store->delete();

        return response()->json([
            'message' => 'deleted successfully'
        ]);
    }

    public function get_products ($id) { 
        $store = Store::find($id);

        if (!$store){ 
            return response()->json([
                'message' => 'store not found'
            ], 404);
        }

        $data = $store->products;

        return response()->json([
            'data' => $data,
            'message' => 'succeed'
        ]);
    }
}
