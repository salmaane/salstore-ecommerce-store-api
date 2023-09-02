<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function monthTotalSales($monthsToSubtract = 0) 
    {
        $monthTotalSales = OrderItem::whereHas('order', fn ($query) =>
            $query->whereMonth('order_date', Carbon::today()->month - abs($monthsToSubtract))->where('status', 'delivered')
        )->count();

        return $monthTotalSales;
    }

    public function totalSales()
    {
        $totalSales = OrderItem::whereHas(
            'order', fn ($query) => $query->where('status', 'delivered')
        )->count();

        return $totalSales;
    }
}
