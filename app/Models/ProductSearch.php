<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductSearch extends Model
{
    use HasFactory;

    protected $table = 'product_searches';

    public $incrementing = false;

    protected $fillable = ['product_id', 'search_text'];

    public static function searchByTerm($term)
    {
        $words = explode(' ', $term);

        $searchQuery = implode(' & ', $words);
        return DB::table('product_searches')
            ->whereRaw("search_vector @@ to_tsquery('simple', ?)", [
                $searchQuery,
            ])
            ->pluck('product_id');
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
