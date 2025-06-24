<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'icon',
        'is_active',
        'min_amount',
        'max_amount',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'min_amount' => 'decimal:2',
            'max_amount' => 'decimal:2',
        ];
    }

    public function exchangeRates()
    {
        return $this->hasMany(ExchangeRate::class, 'to_method_id');
    }

    public function conversionOrders()
    {
        return $this->hasMany(ConversionOrder::class, 'to_method_id');
    }
}
