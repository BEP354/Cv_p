<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\ConversionOrder;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();
            
        // Get both conversion orders (sell) and purchase orders (buy)
        $conversionOrders = Auth::user()->conversionOrders()
            ->with('paymentMethod')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($order) {
                $order->transaction_type = 'sell';
                return $order;
            });
            
        $purchaseOrders = Auth::user()->purchaseOrders()
            ->with('paymentMethod')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($order) {
                $order->transaction_type = 'buy';
                return $order;
            });
        
        // Combine and sort by created_at
        $orders = $conversionOrders->concat($purchaseOrders)
            ->sortByDesc('created_at')
            ->take(10);
        
        return view('dashboard.index', compact('paymentMethods', 'orders'));
    }
}
