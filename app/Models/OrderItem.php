<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'sneaker_id', 'quantity', 'price'
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function sneaker() {
        return $this->belongsTo(Sneaker::class);
    }
}
