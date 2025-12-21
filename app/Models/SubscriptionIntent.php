<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionIntent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan',
        'price',
        'status',
        'order_id',
        'payment_token',
        'redirect_url',
        'midtrans_status',
        'payload',
        'paid_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
