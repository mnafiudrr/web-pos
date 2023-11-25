<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'image',
        'price',
        'stock',
        'shop_id',
        'created_by',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->shop_id = auth()->user()->shop_id;
            $product->created_by = auth()->user()->id;
        });
    }

    /**
     * Get the shop associated with the product.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    /**
     * Get the categories associated with the product.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }
    
    /**
     * Get the user associated with the product.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the product's price.
     */
    public function getPriceAttribute($value)
    {
        return $value;
        // return number_format($value, 0, ',', '.');
    }

    /**
     * Get the product's image.
     */
    public function getImageAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }
}
