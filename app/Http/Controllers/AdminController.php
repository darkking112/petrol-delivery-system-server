<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Report;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Fuels Orders List
    public function getFuelsOrdersList()
    {
        $orders = Order::get()->where('status', 'pending');
        return response()->json([
            'orders' => $orders
        ], 200);
    }

    // Assign Driver to Fuel Order
    public function assignDriverToFuelOrder(Request $request)
    {
        $order = Order::find($request->input('orderID'));
        $order->driverID = $request->input('driverID');
        $order->status = 'Pending Driver Confirmation';
        $order->amount = $request->input('amount');
        $order->save();

        return response()->json([
            'message' => 'Driver assigned to fuel order successfully',
            'order' => $order
        ], 200);
    }

    // Reject Fuel Order
    public function rejectFuelOrder(Request $request)
    {
        $order = Order::find($request->input('orderID'));
        $order->status = 'rejected';
        $order->save();

        return response()->json([
            'message' => 'Fuel order rejected successfully',
            'order' => $order
        ], 200);
    }

    // Generate Monthly Report
    public function generateMonthlyReport(Request $request)
    {
        $month = $request->input('month');
        $orders = Order::where('status', '=', 'delivered')->where('orderDate', 'like', "%" . $month . "%")->get();
        $totalAmount = 0;
        foreach ($orders as $order) {
            $totalAmount += $order->amount;
        }

        $report = Report::create([
            'month' => $month,
            'totalOrders' => count($orders),
            'revenue' => $totalAmount,
            'details' => "Monthly report for month " . $month . " of this year is generated successfully and the total revenue is " . $totalAmount,
            'path' => "reports/" . $month . ".pdf"
        ]);

        return response()->json([
            'message' => 'Monthly report generated successfully',
            'report' => $report
        ], 201);
    }
}
