<?php

namespace App\Http\Controllers;

use App\Models\SuccessLog;
use App\Models\PaymentMethod;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get recent successful transactions for guest display
        $recentSuccess = SuccessLog::orderBy('completed_at', 'desc')
            ->take(10)
            ->get();

        // Get payment methods for preview
        $paymentMethods = PaymentMethod::where('is_active', true)
            ->orderBy('type')
            ->get();

        // Get sample rates for display
        $sampleRates = ExchangeRate::with('paymentMethod')
            ->where('is_active', true)
            ->take(6)
            ->get();

        return view('welcome', compact('recentSuccess', 'paymentMethods', 'sampleRates'));
    }
}
