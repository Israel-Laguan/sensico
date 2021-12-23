<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $table = 'products';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'is_active',
        'brand_id',
        'category_id',
        'technical_specifications',
        'links',
        'related_information',
        'classification',
        'properties'
    ];

    public function categories() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brands() {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

}
