<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressWidgetStyle extends Model
{
    protected $fillable = [
        'user_id',
        'position',
        'widget_shape',
        'bg_color',
        'width',
        'height',
        'open_drawer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
