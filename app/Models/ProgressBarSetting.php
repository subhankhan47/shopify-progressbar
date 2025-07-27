<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressBarSetting extends Model
{
    protected $fillable = [
        'user_id',
        'top_bar_enabled',
        'sticky_widget_enabled',
        'home_page_show',
        'collection_page_show',
        'product_page_show',
        'custom_message',
        'completion_message',
        'animation_enabled',
        'animation_style'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
