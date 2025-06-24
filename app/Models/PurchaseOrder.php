<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'from_method_id',
        'to_currency',
        'to_amount',
        'rate',
        'fee_percentage',
        'fee_amount',
        'admin_fee',
        'subtotal',
        'total_idr',
        'recipient_email',
        'notes',
        'status',
        'payment_proof',
        'admin_notes',
        'processed_by',
        'completed_at',
    ];

    protected $casts = [
        'to_amount' => 'decimal:2',
        'rate' => 'decimal:2',
        'fee_percentage' => 'decimal:4',
        'fee_amount' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total_idr' => 'decimal:2',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'from_method_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'success' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getStatusTextAttribute()
    {
        $texts = [
            'pending' => 'Menunggu Pembayaran',
            'processing' => 'Sedang Diproses',
            'success' => 'Berhasil',
            'failed' => 'Gagal',
            'cancelled' => 'Dibatalkan',
        ];

        return $texts[$this->status] ?? 'Unknown';
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d M Y H:i');
    }

    public function getFormattedCompletedAtAttribute()
    {
        return $this->completed_at ? $this->completed_at->format('d M Y H:i') : null;
    }
}
