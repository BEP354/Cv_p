<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\ExchangeRate;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function getRate(Request $request)
    {
        $request->validate([
            'from_method_id' => 'required|exists:payment_methods,id',
            'to_currency' => 'required|in:paypal,skrill',
            'amount' => 'required|numeric|min:10|max:1000',
        ]);

        // Get buy rate (higher than sell rate)
        $rate = ExchangeRate::where('from_currency', $request->to_currency)
            ->where('to_method_id', $request->from_method_id)
            ->where('is_active', true)
            ->first();

        $paymentMethod = PaymentMethod::find($request->from_method_id);

        if (!$rate) {
            // Gunakan rate default untuk pembelian (lebih tinggi dari rate jual)
            $baseRate = $request->to_currency === 'paypal' ? 15200 : 15100;
            $buyRate = $baseRate + 300; // Add margin for buying
            
            // Different fees based on payment method type
            if ($paymentMethod->type === 'ewallet') {
                $feePercentage = 2.0; // E-wallet fee 2.0%
                $adminFee = 2500;
            } else {
                $feePercentage = 1.5; // Bank fee 1.5%
                $adminFee = 2000;
            }
        } else {
            $buyRate = $rate->rate + 300; // Add margin for buying
            $feePercentage = $rate->fee_percentage * 100;
            $adminFee = $rate->admin_fee;
        }

        // Calculate amounts for buying (reverse calculation)
        $amount = $request->amount; // USD amount to buy
        $subtotal = $amount * $buyRate;
        $feeAmount = $subtotal * ($feePercentage / 100);
        $totalIdr = $subtotal + $feeAmount + $adminFee;

        return response()->json([
            'amount' => $amount,
            'rate' => $buyRate,
            'subtotal' => $subtotal,
            'fee_percentage' => $feePercentage,
            'fee_amount' => $feeAmount,
            'admin_fee' => $adminFee,
            'total_idr' => $totalIdr,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_method_id' => 'required|exists:payment_methods,id',
            'to_currency' => 'required|in:paypal,skrill',
            'to_amount' => 'required|numeric|min:10|max:1000',
            'recipient_email' => 'required|email',
            'recipient_email_confirmation' => 'required|email|same:recipient_email',
            'notes' => 'nullable|string|max:500'
        ]);

        // Get rate and calculate
        $rate = ExchangeRate::where('from_currency', $request->to_currency)
            ->where('to_method_id', $request->from_method_id)
            ->where('is_active', true)
            ->first();

        $paymentMethod = PaymentMethod::find($request->from_method_id);

        if (!$rate) {
            // Gunakan rate default untuk pembelian
            $baseRate = $request->to_currency === 'paypal' ? 15200 : 15100;
            $buyRate = $baseRate + 300;
            
            if ($paymentMethod->type === 'ewallet') {
                $feePercentage = 0.02; // 2.0%
                $adminFee = 2500;
            } else {
                $feePercentage = 0.015; // 1.5%
                $adminFee = 2000;
            }
        } else {
            $buyRate = $rate->rate + 300;
            $feePercentage = $rate->fee_percentage;
            $adminFee = $rate->admin_fee;
        }

        $amount = $request->to_amount;
        $subtotal = $amount * $buyRate;
        $feeAmount = $subtotal * $feePercentage;
        $totalIdr = $subtotal + $feeAmount + $adminFee;

        // Create purchase order
        $order = PurchaseOrder::create([
            'order_code' => 'BUY' . strtoupper(Str::random(8)),
            'user_id' => Auth::id(),
            'from_method_id' => $request->from_method_id,
            'to_currency' => $request->to_currency,
            'to_amount' => $amount,
            'rate' => $buyRate,
            'fee_percentage' => $feePercentage,
            'fee_amount' => $feeAmount,
            'admin_fee' => $adminFee,
            'subtotal' => $subtotal,
            'total_idr' => $totalIdr,
            'recipient_email' => $request->recipient_email,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return redirect()->route('purchase.show', $order->order_code)
            ->with('success', 'Order pembelian berhasil dibuat! Silakan lakukan pembayaran.');
    }

    public function show($orderCode)
    {
        $order = PurchaseOrder::where('order_code', $orderCode)
            ->where('user_id', Auth::id())
            ->with('paymentMethod')
            ->firstOrFail();

        return view('purchase.show', compact('order'));
    }

    public function uploadProof(Request $request, $orderCode)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $order = PurchaseOrder::where('order_code', $orderCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/proofs'), $filename);
            
            $order->update([
                'payment_proof' => 'uploads/proofs/' . $filename,
                'status' => 'processing'
            ]);
        }

        return back()->with('success', 'Bukti pembayaran berhasil diupload!');
    }
}
