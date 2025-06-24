<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuccessLog extends Model
{
    use HasFactory;

    // Disable updated_at since we don't have it in the table
    public $timestamps = false;
    
    protected $fillable = [
        'order_code',
        'from_currency',
        'from_amount',
        'to_method',
        'total_idr',
        'user_initial',
        'completed_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'from_amount' => 'decimal:2',
            'total_idr' => 'decimal:2',
            'completed_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }
}
