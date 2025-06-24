<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_currency',
        'to_method_id',
        'rate',
        'fee_percentage',
        'admin_fee',
        'is_active',
        'updated_by'
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'fee_percentage' => 'decimal:4',
        'admin_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'to_method_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
