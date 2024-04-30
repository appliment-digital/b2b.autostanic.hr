<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductController extends BaseController
{
    public function getProductsByCategoryId()
    {
        try {
            $categoryId = 39877;
            $query = DB::connection('webshopdb')
                ->table('dbo.Product')
                ->select(
                    'Product.Id',
                    'Product.Name',
                    'Product.ShortDescription',
                    'Product.FullDescription',
                    'Product.Sku',
                    'Product.StockQuantity',
                    'Product.Price',
                    'Product.OldPrice',
                    'Product.IsNewPart',
                    'Product.IsUsedPart',
                    'Product.ManufacturerName'
                )
                ->join(
                    'Product_Category_Mapping',
                    'Product.Id',
                    '=',
                    'Product_Category_Mapping.ProductId'
                )
                ->where('Product_Category_Mapping.CategoryId', $categoryId)
                ->where('Product.Deleted', 0)
                ->where('Product.Published', 1)
                ->groupBy(
                    'Product.Id',
                    'Product.Name',
                    'Product.ShortDescription',
                    'Product.FullDescription',
                    'Product.Sku',
                    'Product.StockQuantity',
                    'Product.Price',
                    'Product.OldPrice',
                    'Product.IsNewPart',
                    'Product.IsUsedPart',
                    'Product.ManufacturerName'
                )
                ->take(10)
                ->orderBy('DisplayOrder')
                ->get();

            return $query;
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function getCarTypesForProduct()
    {
        $productId = 44038;
        $carTypes = DB::connection('webshopdb')
            ->table('dbo.Product_CarType_Mapping')
            ->select('CarType.*')
            ->join(
                'CarType',
                'Product_CarType_Mapping.CarTypeId',
                '=',
                'CarType.Id'
            )
            ->where('Product_CarType_Mapping.ProductId', $productId)
            ->where('CarType.Deleted', 0)
            ->where('CarType.Published', 1)
            ->get();

        return $carTypes;
    }

    public function getProduct($id)
    {
        try {
            $id = $conn = DB::connection('sqlsrv');
            $query = $conn->table('dbo.Product')->where('Id', '2083991');
            $data = $query->get();

            $dataFormatted = $this->convertKeysToCamelCase($data);

            return response()->json($dataFormatted[0]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error fetching records: ' . $e->getMessage());
        }
    }

    public function test()
    {
        $categoryId = 39877;
        $query = DB::connection('webshopdb')
            ->table('dbo.Product')
            ->select(
                'dbo.Product.Id',
                'dbo.Product.Name',
                'dbo.Product.ShortDescription',
                'dbo.Product.FullDescription',
                'dbo.Product.Sku',
                'dbo.Product.StockQuantity',
                'dbo.Product.Price',
                'dbo.Product.OldPrice',
                'dbo.Product.IsNewPart',
                'dbo.Product.IsUsedPart',
                'dbo.Product.ManufacturerName'
            )
            ->join(
                'dbo.Product_Category_Mapping',
                'dbo.Product.Id',
                '=',
                'dbo.Product_Category_Mapping.ProductId'
            )
            ->where('dbo.Product_Category_Mapping.CategoryId', $categoryId)
            ->where('dbo.Product.Deleted', 0)
            ->where('dbo.Product.Published', 1)
            ->groupBy(
                'dbo.Product.Id',
                'dbo.Product.Name',
                'dbo.Product.ShortDescription',
                'dbo.Product.FullDescription',
                'dbo.Product.Sku',
                'dbo.Product.StockQuantity',
                'dbo.Product.Price',
                'dbo.Product.OldPrice',
                'dbo.Product.IsNewPart',
                'dbo.Product.IsUsedPart',
                'dbo.Product.ManufacturerName'
            )
            ->take(10)
            ->orderBy('DisplayOrder')
            ->get();

        return $query;
    }
}
