<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table='product_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'category_id',
        'created_by',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($productCategory) {
            $productCategory->created_by = auth()->user()->id;
        });
    }

    /**
     * Get the product associated with the product category.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the category associated with the product category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user associated with the product category.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
