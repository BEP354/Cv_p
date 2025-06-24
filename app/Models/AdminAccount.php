<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'account_number',
        'account_name',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'paypal' => 'fab fa-paypal',
            'skrill' => 'fas fa-wallet',
            'bank' => 'fas fa-university',
            'ewallet' => 'fas fa-mobile-alt',
            default => 'fas fa-credit-card'
        };
    }

    public function getTypeBadgeAttribute()
    {
        return match($this->type) {
            'paypal' => 'bg-blue-100 text-blue-800',
            'skrill' => 'bg-red-100 text-red-800',
            'bank' => 'bg-green-100 text-green-800',
            'ewallet' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
