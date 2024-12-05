<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_cost'
    ];

    function user ( ){
        return $this->belongsTo(User::class);
    }

    function products( ) { 
        return $this->belongsToMany(Product::class, 'orders_products')->withPivot('id', 'quantity');
    }
}
