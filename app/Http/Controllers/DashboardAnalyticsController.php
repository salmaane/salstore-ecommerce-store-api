<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardAnalyticsController extends Controller
{
    public function index(Request $request) {
        $salesController = new SalesController;
        $userAnalyticsController = new UserAnalyticsController;
        return [
            'totalSales' => $salesController->totalSales(),
            'monthTotalSales' => $salesController->monthTotalSales(),
            'revenue' => $salesController->revenue(),
            'monthRevenue' => $salesController->monthRevenue(),
            'dailyAverageOrder' => $salesController->dailyAverageOrder(),
            'topSellingProducts' => $salesController->topSellingProducts(),
            'ordersPerDay' => $salesController->ordersPerDay(),
            'salesPerDay' => $salesController->salesPerDay(),
            'lowStockProducts' => $salesController->lowStockProducts(),
            'newUsers' => $userAnalyticsController->newUsers(),
            'usersVisits' => $userAnalyticsController->usersVisits(),
        ];
    }
}
