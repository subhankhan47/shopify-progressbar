<?php

namespace App\Models;
use Osiset\ShopifyApp\Contracts\ShopModel as IShopModel;
use Osiset\ShopifyApp\Traits\ShopModel;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements IShopModel
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, ShopModel;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function progressBarSetting()
    {
        return $this->hasOne(ProgressBarSetting::class);
    }

    public function progressBarStyle()
    {
        return $this->hasOne(ProgressBarStyle::class);
    }

    public function progressWidgetStyle()
    {
        return $this->hasOne(ProgressWidgetStyle::class);
    }

    public function progressDrawerStyle()
    {
        return $this->hasOne(ProgressDrawerStyle::class);
    }

    public function thresholds()
    {
        return $this->hasMany(Threshold::class);
    }

}
