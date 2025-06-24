<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use App\Models\ExchangeRate;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {
        // Create payment methods
        $methods = [
            // E-Wallets
            ['name' => 'DANA', 'type' => 'ewallet', 'code' => 'DANA', 'min_amount' => 50000, 'max_amount' => 20000000],
            ['name' => 'ShopeePay', 'type' => 'ewallet', 'code' => 'SHOPEE', 'min_amount' => 50000, 'max_amount' => 20000000],
            ['name' => 'GoPay', 'type' => 'ewallet', 'code' => 'GOPAY', 'min_amount' => 50000, 'max_amount' => 20000000],
            ['name' => 'OVO', 'type' => 'ewallet', 'code' => 'OVO', 'min_amount' => 50000, 'max_amount' => 20000000],
            
            // Banks
            ['name' => 'Bank BCA', 'type' => 'bank', 'code' => 'BCA', 'min_amount' => 100000, 'max_amount' => 50000000],
            ['name' => 'Bank Mandiri', 'type' => 'bank', 'code' => 'MANDIRI', 'min_amount' => 100000, 'max_amount' => 50000000],
            ['name' => 'Bank BNI', 'type' => 'bank', 'code' => 'BNI', 'min_amount' => 100000, 'max_amount' => 50000000],
            ['name' => 'Bank BRI', 'type' => 'bank', 'code' => 'BRI', 'min_amount' => 100000, 'max_amount' => 50000000],
        ];

        foreach ($methods as $method) {
            PaymentMethod::create($method);
        }

        // Create exchange rates
        $paymentMethods = PaymentMethod::all();
        
        foreach ($paymentMethods as $method) {
            // PayPal rates
            ExchangeRate::create([
                'from_currency' => 'paypal',
                'to_method_id' => $method->id,
                'rate' => $method->type === 'ewallet' ? 15500 : 15600,
                'fee_percentage' => $method->type === 'ewallet' ? 0.03 : 0.025,
                'admin_fee' => $method->type === 'ewallet' ? 5000 : 10000,
                'is_active' => true,
            ]);
            
            // Skrill rates
            ExchangeRate::create([
                'from_currency' => 'skrill',
                'to_method_id' => $method->id,
                'rate' => $method->type === 'ewallet' ? 15400 : 15500,
                'fee_percentage' => $method->type === 'ewallet' ? 0.03 : 0.025,
                'admin_fee' => $method->type === 'ewallet' ? 5000 : 10000,
                'is_active' => true,
            ]);
        }
    }
}
