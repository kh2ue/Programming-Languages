<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'name',
        'price',
        'available_quantity',
        'production_date',
        'expiry_date',
    ];

    function store () { 
        return $this->belongsTo(Store::class);
    }

    function favorite_to( ){
        return $this->belongsToMany(User::class, 'favorites')->withPivot('id');
    }

    function orders ( ){
        return $this->belongsToMany(Order::class, 'orders_products')->withPivot('id', 'quantity');
    }

    function cart_to( ){
        return $this->belongsToMany(User::class, 'shoppoings')->withPivot('id', 'quantity');
    }
}
