<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShoppingRequest;
use App\Http\Requests\UpdateShoppingsRequest;
use App\Models\Order;
use App\Models\Orders_products;
use App\Models\Product;
use App\Models\Shopping;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ShoppingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $id = auth()->user()->id;
        $user = User::find($id); 

        $products = $user->cart_products()->get();

        return response()->json([
            'data' => $products,
            'message' => 'succeed'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShoppingRequest $request)
    {
        $data = $request->validated();

        $userid = auth()->user()->id;
        if ( Shopping::where('user_id', $userid)
            ->where('product_id', $data['product_id'])->first() != null) { 
                return response()->json([
                    'message' => 'product already exists, try to update it :) '
                ], 400);
            }

        Shopping::create([
            'user_id' => auth()->user()->id,
            'product_id' => $data['product_id'],
            'quantity' => $data ['quantity']
        ]);

        return response()->json([
            'data' => $data, 
            'message' => 'created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        return "hehe";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShoppingsRequest $request, $id)
    {
        $data = $request->validated();
        $product = Shopping::find($id); 
        
        if (!$product) { 
            return response()->json([
                'message' => 'not found'
            ], 404);
        }
        
        if ($product['user_id'] != auth()->user()->id){ 
            return response()->json([
                'message' => 'not allowed'
            ], 400);
        }

        $product->update($data); 

        return response()->json([
            'data' => $product, 
            'message' => 'updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Shopping::find($id);
        
        if (!$product) { 
            return response()->json([
                'message' => 'not found'
            ], 404);
        }
        
        if ($product['user_id'] != auth()->user()->id){ 
            return response()->json([
                'message' => 'not allowed'
            ], 400);
        }

        $product->delete(); 

        return response()->json([
            'message' => 'deleted successfully'
        ]);
    }

    /**
     * make your order
     */
     public function apply () { 
        $id = auth()->user()->id;
        $user = User::find($id); 
        $products = $user->cart_products()->get()
            ->map(function ($product) {
                return $product->pivot;
            });

        foreach ($products as $product) {
            $sho = Product::find($product->product_id);

            if ($product->quantity > $sho->available_quantity) 
            { 
                $productName = $sho['name'];
                return response()->json([
                     'message' => "You can't get this quantity of product '{$productName}'" 
                    ], 400);
            }
        }

        $order = Order::create([
            'user_id' => $id,
            'total_cost' => 0
        ]);

        foreach ($products as $product) {
            $sho = Product::find($product->product_id);

            $cua = $sho['available_quantity'] - $product->quantity;
            $sho->update([
                'available_quantity' => $cua
            ]);

            $cua = $product->quantity * $sho->price + $order['total_cost'];
            $order->update(['total_cost' => $cua]);

            Orders_products::create([
                'order_id' => $order['id'],
                'product_id' => $product->product_id,
                'quantity' => $product->quantity,
                'current_price' => $sho->price
            ]); 
        }
        
        DB::table('shoppings')->where('user_id', $id)->delete();

        return response()->json([
            'message' => 'order compleated successfully'
        ]);
    }
}
