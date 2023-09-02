<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function totalSales() {
        $totalSales = OrderItem::whereHas('order',function($query) {
            $query->where('status','delivered');
        })->count();

        return $totalSales;
    }
}
