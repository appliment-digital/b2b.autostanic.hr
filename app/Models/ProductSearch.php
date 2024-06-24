<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSearch extends Model
{
    use HasFactory;

    protected $table = 'product_searches';

    public $incrementing = false;

    protected $fillable = ['product_id', 'search_text'];

    public static function searchByTerm($term)
    {
        $term = '%' . $term . '%';
        return ProductSearch::where('search_text', 'ilike', $term)->pluck(
            'product_id'
        );
    }

    public static function add($id, $searchText)
    {
        $productSearch = new self();
        $productSearch->product_id = $id;
        $productSearch->search_text = $searchText;

        $productSearch->save();

        return $productSearch;
    }

    public static function updateProductSearch($id, $searchText)
    {
        $productSearch = self::find($id);
        $productSearch->search_text = $searchText;

        $productSearch->save();

        return $productSearch;
    }
}
