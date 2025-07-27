<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Threshold extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'reward_type',
        'product_id',
        'priority',
        'auto_add_product',
        'shipping_regions'
    ];

    protected $casts = [
        'shipping_regions' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
