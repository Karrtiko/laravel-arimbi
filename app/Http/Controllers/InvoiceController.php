<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function show(Order $order)
    {
        $order->load('items.itemable');

        return view('invoices.show', [
            'order' => $order,
        ]);
    }

    public function bulk(Request $request)
    {
        $ids = explode(',', $request->get('ids', ''));
        $orders = Order::whereIn('id', $ids)->with('items.itemable')->get();

        return view('invoices.bulk', [
            'orders' => $orders,
        ]);
    }
}
