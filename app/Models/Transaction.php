<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'from_currency',
        'to_currency',
        'amount',
        'rate',
        'fee',
        'total',
        'status',
        'notes',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'rate' => 'decimal:4',
            'fee' => 'decimal:2',
            'total' => 'decimal:2',
            'completed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
