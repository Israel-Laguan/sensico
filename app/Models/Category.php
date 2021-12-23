<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $table = 'categories';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    use HasFactory;
    protected $fillable = [
        'name', 'slug', 'icon', 'is_active','section_id'
    ];

    public function section() {
        return $this->belongsTo(Section::class, 'section_id')->where('state','>', 0);
    }
}
