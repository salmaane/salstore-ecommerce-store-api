<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Sneaker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function topSellingProducts() {
        $topSellingProducts = OrderItem::join('sneakers', 'order_items.sneaker_id', '=', 'sneakers.id')
                            ->select('sneaker_id as id', 'sneakers.title', 'sneakers.brand',
                                'sneakers.quantity as stockQuantity', 'sneakers.colorway',
                                'sneakers.releaseDate', 'order_items.price',
                                DB::raw('count(*) as soldQuantity'),
                                DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
                            )
                            ->groupBy(
                                'sneaker_id', 'sneakers.title',
                                'sneakers.brand', 'order_items.price',
                                'sneakers.colorway', 'sneakers.quantity',
                                'sneakers.releaseDate'
                            )
                            ->orderByDesc('soldQuantity')
                            ->get();

        return $topSellingProducts;
    }

    public function ordersPerDay($daysToSubtract = 7) {
        $ordersPerDay = [];
        
        $day = Carbon::now();

        for($i = 0; $i < $daysToSubtract; $i++) {
            $day = $day->subDay();
            $ordersPerDay[$day->toDateString()] = Order::where('order_date', $day->toDateString())->count();
        }

        return $ordersPerDay;
    }

    public function lowStockProducts($limit = 10) {
        $products = Sneaker::orderBy('quantity')
                            ->with([
                                'media' => function ($query) {
                                    $query->select(['id', 'sneaker_id', 'imageUrl', 'smallImageUrl', 'thumbUrl']);
                                }
                            ])
                            ->paginate($limit, ['id', 'title', 'brand', 'colorway', 'gender', 'retailPrice', 'releaseDate', 'quantity']);

        return $products;
    }
}
