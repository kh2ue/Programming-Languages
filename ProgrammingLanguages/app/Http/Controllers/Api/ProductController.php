<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Product::get(); 
        
        return response()->json([
            'data' => $data,
            'message' => 'succeed'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        if($data['production_date'] >= $data['expiry_date']) { 
            return response()->json([
                'message' => "expiry date can't be earlier than production date"
            ], 400);
        }

        $product = Product::create($data);

        return response()->json([
            'data' => $product,
            'message' => 'created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::find($id); 

        if(!$product){ 
            return response()->json([
                'message' => "product not found"
            ], 404);
        }

        return response()->json([
            'data' => $product,
            'message' => 'succeed'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $data = $request->validated();
        $product = Product::find($id); 

        if (!$product) { 
            return response()->json([
                'message' => "product not found"
            ], 404); 
        }

        $product->update($data); 

        return response()->json ([
            'data' => $product,
            'message' => 'updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id); 

        if (!$product) { 
            return response()->json([
                'message' => "product not found"
            ], 404); 
        }

        $product->delete(); 

        return response()->json ([
            'message' => 'deleted successfully'
        ]);
    }
}
