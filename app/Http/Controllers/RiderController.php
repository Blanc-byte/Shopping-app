<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class RiderController extends Controller
{
    public function updateStatus($id)
    {
        // Find the order by its ID
        $order = Order::findOrFail($id);
        
        // Update the order status to 'out_for_delivery'
        $order->status = 'out_for_delivery';
        $order->save();
        
        // Insert into deliveries table
        DB::table('deliveries')->insert([
            'order_id' => $order->id,
            'rider_id' => Auth::id(), // Use the currently authenticated rider's ID
            'delivery_date' => now() // Optional: Insert the current timestamp as the delivery date
        ]);

        return redirect()->back()->with('success', 'Order status updated to out for delivery.');
    }
    public function index()
    {
        $pendingOrders = collect(DB::select(
            'SELECT products.name, products.description, orders.quantity, orders.total, orders.created_at AS Date, 
                orders.status, users.name AS customerName, orders.id
            FROM orders
            JOIN users ON orders.customer_id = users.id
            JOIN products ON orders.product_id = products.id
            WHERE orders.status = "placed"
        '));
            
        return view('rider.dashboard',[
            'pendingOrders' => $pendingOrders
        ]);
    }
    /**
     * Shows the orders that are to be delivered by the rider.
     * 
     * @return \Illuminate\Http\Response
     */
    public function tobedelivered()
    {
        $userId = auth()->id();
        
        $toBeDeliveredOrders = collect(DB::select(
                    'SELECT p.name, p.description, o.quantity, o.total, d.delivery_date as Date, d.id, u.name as CusName, o.id as origOrdersId
                    FROM deliveries d
                    JOIN orders o ON d.order_id = o.id
                    JOIN products p ON o.product_id = p.id
                    JOIN users u ON u.id = o.customer_id
                    WHERE d.status = "pending" AND d.rider_id = ?', [$userId]
        ));
        return view('rider.tobedelivered',[
            'toBeDeliveredOrders' => $toBeDeliveredOrders
        ]);
    }
    public function delivered()
    {
        $userId = auth()->id();

        $DeliveredOrders = collect(DB::select(
                    'SELECT p.name, p.description, o.quantity, o.total, d.delivery_date as Date, d.id, u.name as CusName
                    FROM deliveries d
                    JOIN orders o ON d.order_id = o.id
                    JOIN products p ON o.product_id = p.id
                    JOIN users u ON u.id = o.customer_id
                    WHERE d.status = "delivered" AND d.rider_id = ?', [$userId]
        ));
        return view('rider.delivered',[
            'DeliveredOrders' => $DeliveredOrders
        ]);
    }
    public function markAsDelivered($id, $origOrdersId)
    {
        DB::table('deliveries')
            ->where('id', $id)
            ->update(['status' => 'delivered']);

        DB::table('orders')
            ->where('id', $origOrdersId)
            ->update(['status' => 'delivered']);   

        return response()->json(['success' => true, 'message' => 'Order marked as delivered.']);
    }

}
