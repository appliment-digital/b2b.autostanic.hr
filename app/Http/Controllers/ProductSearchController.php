<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\ProductSearch;

class ProductSearchController extends BaseController
{
    public function addSearchText($productId)
    {
        //getting data form database
        $productData = DB::connection('webshopdb')
            ->table('dbo.Product')
            ->select(
                'Product.Id AS id',
                DB::raw('
                    CONCAT(
                        Product.Name,
                        CASE WHEN Product.Sku <> \'\' THEN CONCAT(\', \', Product.Sku) ELSE \'\' END,
                        CASE WHEN Product.ManufacturerName <> \'\' THEN CONCAT(\', \', Product.ManufacturerName) ELSE \'\' END,
                        CASE WHEN ProductSearch.EntityValue <> \'\' THEN CONCAT(\', \', ProductSearch.EntityValue) ELSE \'\' END,
                        CASE WHEN EXISTS (
                            SELECT 1
                            FROM Product_OEMCode_Mapping
                            WHERE Product_OEMCode_Mapping.ProductId = Product.Id
                            AND Product_OEMCode_Mapping.IsOEMCode = 1
                        ) THEN CONCAT(\', \', (
                            SELECT STRING_AGG(Product_OEMCode_Mapping.OEMCodeDenormalized, \', \')
                            FROM Product_OEMCode_Mapping
                            WHERE Product_OEMCode_Mapping.ProductId = Product.Id
                        )) ELSE \'\' END
                    ) AS search_text
                ')
            )
            ->leftJoin(
                'B2B.ProductSearch',
                'Product.Id',
                '=',
                'ProductSearch.ProductId'
            )
            ->where('Product.Id', $productId)
            ->first();
        return ProductSearch::add($productId, $productData->search_text);
    }
}
