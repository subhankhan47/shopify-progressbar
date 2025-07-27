<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressDrawerStyle extends Model
{
    protected $fillable = [
        'user_id',
        'filled_progress_color',
        'bg_color',
        'layout',
        'message_position',
        'animation',
        'font_color',
        'font_size',
        'show_products_in_bar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
