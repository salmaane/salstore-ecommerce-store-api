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

    public function revenue() {
        $revenue = Order::where('status', 'delivered')->sum('total_amount');

        return $revenue;
    }

    public function monthRevenue($monthsToSubtract = 0) {
        $monthRevenue = Order::whereMonth('order_date', Carbon::today()->month - abs($monthsToSubtract))
                        ->where('status', 'delivered')->sum('total_amount');

        return $monthRevenue;
    }

    public function dailyAverageOrder($monthsToSubtract = 0) {
        $currentDate = Carbon::today();

        if($monthsToSubtract === 0) {
            $average = floor(Order::whereMonth('order_date', $currentDate->month)->count() / $currentDate->day);
        } else {
            $average = floor(Order::whereMonth('order_date', $currentDate->month - abs($monthsToSubtract) )->count() / 30);
        }

        return $average;
    }
}
