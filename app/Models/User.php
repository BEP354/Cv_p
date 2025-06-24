<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function conversionOrders()
    {
        return $this->hasMany(ConversionOrder::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Get all transactions (both conversion and purchase)
     */
    public function allTransactions()
    {
        $conversions = $this->conversionOrders()->get()->map(function($order) {
            $order->transaction_type = 'sell';
            $order->type_label = 'Jual';
            return $order;
        });

        $purchases = $this->purchaseOrders()->get()->map(function($order) {
            $order->transaction_type = 'buy';
            $order->type_label = 'Beli';
            return $order;
        });

        return $conversions->concat($purchases)->sortByDesc('created_at');
    }
}
