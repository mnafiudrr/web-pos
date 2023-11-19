<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table='categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'shop_id',
        'created_by',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->shop_id = auth()->user()->shop_id;
            $category->created_by = auth()->user()->id;
        });
    }

    /**
     * Get the shop associated with the category.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the parent category associated with the category.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the child categories associated with the category.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get the products associated with the category.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    /**
     * Get the user associated with the category.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
