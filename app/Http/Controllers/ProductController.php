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
            $categoryId = 39884;
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
                    'dbo.Product.ManufacturerName',
                    DB::raw(
                        "STRING_AGG(dbo.OEMCode.OEMCodeDenormalized, ',') AS OEMCodes"
                    ), // Concatenating OEMCodeDenormalized values
                    DB::raw(
                        "STRING_AGG(dbo.SpecificationAttributeOption.Name, ',') AS SpecificationAttributeNames"
                    ) // Concatenating specification attribute names
                )
                ->join(
                    'dbo.Product_Category_Mapping',
                    'dbo.Product.Id',
                    '=',
                    'dbo.Product_Category_Mapping.ProductId'
                )
                ->join(
                    'dbo.Product_OEMCode_Mapping',
                    'dbo.Product.Id',
                    '=',
                    'dbo.Product_OEMCode_Mapping.ProductId'
                )
                ->join(
                    'dbo.OEMCode',
                    'dbo.Product_OEMCode_Mapping.OEMCodeId',
                    '=',
                    'dbo.OEMCode.Id'
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
            ->select('dbo.CarType.*')
            ->join(
                'dbo.CarType',
                'dbo.Product_CarType_Mapping.CarTypeId',
                '=',
                'dbo.CarType.Id'
            )
            ->where('dbo.Product_CarType_Mapping.ProductId', $productId)
            ->where('dbo.CarType.Deleted', 0)
            ->where('dbo.CarType.Published', 1)
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
}
