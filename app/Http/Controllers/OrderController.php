<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {   
        $limit = $request->limit ?? 10;
        
        if($request->status) {
            return Order::with('user')->where('status', $request->status)->orderBy('order_date')->paginate($limit);
        }

        return Order::with('user')->orderBy('order_date')->paginate($limit);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => ['string', 'in:pending,shipped,delivered']
        ]);

        $order = Order::find($id);

        $order->update($request->all());

        return $this->success($order,200);
    }

    public function destroy(string $id)
    {
        //
    }
}
