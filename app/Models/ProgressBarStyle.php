<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressBarStyle extends Model
{
    protected $fillable = [
        'user_id',
        'filled_progress_color',
        'bg_color',
        'message_position',
        'font_color',
        'font_size',
        'border_radius',
        'show_products_in_bar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
