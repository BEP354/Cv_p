<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\ExchangeRate;
use App\Models\ConversionOrder;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ConversionController extends Controller
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

    public function getRate(Request $request)
    {
        $request->validate([
            'from_currency' => 'required|in:paypal,skrill',
            'to_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $rate = ExchangeRate::where('from_currency', $request->from_currency)
            ->where('to_method_id', $request->to_method_id)
            ->where('is_active', true)
            ->first();

        if (!$rate) {
            // Use default rates if no rate found
            $baseRate = $request->from_currency === 'paypal' ? 15200 : 15100;
            $feePercentage = 2.5;
            $adminFee = 5000;
        } else {
            $baseRate = $rate->rate;
            $feePercentage = $rate->fee_percentage * 100;
            $adminFee = $rate->admin_fee;
        }

        $paymentMethod = PaymentMethod::find($request->to_method_id);
    
        // Calculate amounts
        $fromAmount = $request->amount;
        $grossIdr = $fromAmount * $baseRate;
        $feeAmount = $grossIdr * ($feePercentage / 100);
        $totalIdr = $grossIdr - $feeAmount - $adminFee;

        // Check min/max limits
        if ($paymentMethod && $totalIdr < $paymentMethod->min_amount) {
            return response()->json([
                'error' => 'Minimum transfer Rp ' . number_format($paymentMethod->min_amount, 0, ',', '.')
            ], 400);
        }

        if ($paymentMethod && $totalIdr > $paymentMethod->max_amount) {
            return response()->json([
                'error' => 'Maksimum transfer Rp ' . number_format($paymentMethod->max_amount, 0, ',', '.')
            ], 400);
        }

        return response()->json([
            'rate' => $baseRate,
            'fee_percentage' => $feePercentage,
            'fee_amount' => $feeAmount,
            'admin_fee' => $adminFee,
            'gross_idr' => $grossIdr,
            'total_idr' => $totalIdr,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_currency' => 'required|in:paypal,skrill',
            'from_amount' => 'required|numeric|min:1',
            'to_method_id' => 'required|exists:payment_methods,id',
            'recipient_name' => 'required|string|max:255',
            'recipient_account' => 'required|string|max:100',
            'sender_email' => 'required|email',
        ]);

        $rate = ExchangeRate::where('from_currency', $request->from_currency)
            ->where('to_method_id', $request->to_method_id)
            ->where('is_active', true)
            ->first();

        if (!$rate) {
            // Use default rates if no rate found
            $baseRate = $request->from_currency === 'paypal' ? 15200 : 15100;
            $feePercentage = 0.025; // 2.5%
            $adminFee = 5000;
        } else {
            $baseRate = $rate->rate;
            $feePercentage = $rate->fee_percentage;
            $adminFee = $rate->admin_fee;
        }

        // Calculate amounts
        $fromAmount = $request->from_amount;
        $grossIdr = $fromAmount * $baseRate;
        $feeAmount = $grossIdr * $feePercentage;
        $totalIdr = $grossIdr - $feeAmount - $adminFee;

        $paymentMethod = PaymentMethod::find($request->to_method_id);
        
        // Check limits
        if ($paymentMethod && ($totalIdr < $paymentMethod->min_amount || $totalIdr > $paymentMethod->max_amount)) {
            return back()->withErrors(['amount' => 'Jumlah tidak sesuai dengan limit transfer']);
        }

        // Create order
        $order = ConversionOrder::create([
            'order_code' => 'SELL' . strtoupper(Str::random(8)),
            'user_id' => Auth::id(),
            'from_currency' => $request->from_currency,
            'from_amount' => $fromAmount,
            'to_method_id' => $request->to_method_id,
            'rate' => $baseRate,
            'fee_percentage' => $feePercentage,
            'fee_amount' => $feeAmount,
            'admin_fee' => $adminFee,
            'gross_idr' => $grossIdr,
            'total_idr' => $totalIdr,
            'recipient_name' => $request->recipient_name,
            'recipient_account' => $request->recipient_account,
            'sender_email' => $request->sender_email,
            'status' => 'pending',
        ]);

        return redirect()->route('conversion.show', $order->order_code)
            ->with('success', 'Order berhasil dibuat! Silakan lakukan pembayaran.');
    }

    public function show($orderCode)
    {
        $order = ConversionOrder::where('order_code', $orderCode)
            ->where('user_id', Auth::id())
            ->with('paymentMethod')
            ->firstOrFail();

        return view('conversion.show', compact('order'));
    }

    public function uploadProof(Request $request, $orderCode)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $order = ConversionOrder::where('order_code', $orderCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($order->status !== 'pending') {
            return back()->withErrors(['error' => 'Order sudah tidak dapat diubah']);
        }

        // Store image
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/proofs'), $filename);
            
            $order->update([
                'payment_proof' => 'uploads/proofs/' . $filename,
                'status' => 'processing'
            ]);
        }

        return back()->with('success', 'Bukti pembayaran berhasil diupload! Order akan segera diproses.');
    }
}
