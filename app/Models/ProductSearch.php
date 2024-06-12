<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSearch extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'search_text'];

    public static function searchByTerm($term)
    {
        $term = '%' . $term . '%';
        return ProductSearch::where('search_text', 'ilike', $term)->pluck(
            'product_id'
        );
    }
}
