<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $table = 'shops';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'phone',
        'address',
        'owner',
        'created_by',
    ];

    // before save
    public static function boot()
    {
        parent::boot();

        static::creating(function ($shop) {
            $shop->slug = strtolower($shop->name) . '-' . $shop->owner;
        });
    }

    /**
     * Get the owner associated with the shop.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner');
    }

    /**
     * Get the creator associated with the shop.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
