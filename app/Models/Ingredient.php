<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'max_amount', 'current_amount', 'alert_email_sent'];

    /**
     *--------------------------------------------------------------------------
     * Model Relations
     *--------------------------------------------------------------------------
     */

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_ingredient', 'ingredient_id', 'product_id')
            ->withPivot('amount');
    }
}
