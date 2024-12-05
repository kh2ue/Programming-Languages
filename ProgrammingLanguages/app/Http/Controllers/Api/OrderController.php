<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrdersRequest;
use App\Models\Order;
use App\Models\Orders_products;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $id = auth()->user()->id;
        $user = User::find($id);

        $data = $user->orders()->get();

        return response()->json([
            'data' => $data,
            'message' => 'succeed'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Order::find($id);

        if (!$order) { 
            return response()->json([
                'message' => 'order not found'
            ], 404);
        }

        if($order['user_id'] != auth()->user()->id){
            return response()->json([
                'message' => 'not allowed'
            ], 400);
        }

        $data = $order->products()->get();

        return response()->json([
            'data' => $data,
            'message' => 'succeed'
        ]);
    }

    public function update(UpdateOrdersRequest $request, $id)
    {
        $data = $request->validated();
        $order_product = Orders_products::find($id);

        if (!$order_product){
            return response()->json([
                'message' => 'not found'
            ], 404);
        }

        $orderid =  $order_product['order_id'];
        $order = Order::find($orderid);

        if ($order['user_id'] != auth()->user()->id){
            return response()->json([
                'message' => 'not allowed'
            ], 400);
        }

        $product = Product::find($order_product['product_id']);
        $newAvailable = $product->available_quantity + $order_product['quantity'];

        if ($data['quantity'] > $newAvailable) { 
            $productName = $product->name;
            return response()->json([
                'message' => "You can't get this quantity of product '{$productName}'" 
               ], 400);
        }

        $newAvailable = $newAvailable - $data['quantity'];
        $product->update([
            'available_quantity' => $newAvailable
        ]);

        if ($data['quantity'] > $order_product['quantity']){ 
            $cost =  $order['total_cost'] + ($data['quantity'] - $order_product['quantity']) * $product['price'];
            $order->update([
                'total_cost' => $cost
            ]);

            $cur = Orders_products::create([
                'order_id' => $order['id'],
                'product_id' => $product->id,
                'quantity' => $data['quantity'] - $order_product['quantity'],
                'current_price' => $product->price
            ]); 

            return response()->json([
                'data' => $cur,
                'message' => 'updated successfully'
            ]);
        }
        else {
            $cost =  $order['total_cost'] - $order_product['quantity'] * $order_product['current_price'];
            $order->update([
                'total_cost' => $cost + $data['quantity'] * $order_product['current_price']
            ]);

            $order_product->update ([
                'quantity' => $data['quantity']
            ]);
        }

        DB::table('orders_products')->where('quantity', 0)->delete();
        
        if ($data['quantity'] == 0 ){
            return response()->json([
                'message' => 'deleted successfully'
            ]);
        }

        return response()->json([
            'data' => $order_product,
            'message' => 'updated successfully'
        ]);

    }

    public function destroy($id)
    {
        $order = Order::find($id); 

        if (!$order) { 
            return response()->json([
                'message' => 'order not found'
            ], 404);
        }

        if (!$order['user_id'] != auth()->user()->id) { 
            return response()->json([
                'message' => ' not allowed'
            ], 400);
        }

        $order_products = $order->products()->get()
            ->map(function ($product){
                return $product->pivot;
            });

        
        foreach($order_products as $op){ 
            $product = Product::find($op['product_id']);
            $product->update([
               'available_quantity' => $product['available_quantity'] + $op['quantity']
            ]);
        }

        DB::table('orders_products')->where('order_id', $id)->delete();
        $order->delete();

        return response()->json([
            'message' => 'order deleted successfully'
        ]);
    }
}
