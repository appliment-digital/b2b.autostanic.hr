<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class SupplierController extends BaseController
{
    public function getAll()
    {
        try {
            $query = DB::connection('webshopdb')
                ->table('dbo.Supplier')
                ->select('Supplier.Id', 'Supplier.Name')
                ->get();

            return $this->convertKeysToCamelCase($query);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function getCategoriesForSupplier($id)
    {
        try {
            $query = DB::connection('webshopdb')
                ->table('dbo.Supplier')
                ->join('dbo.Product', 'Supplier.Id', '=', 'Product.SupplierId')
                ->join(
                    'dbo.Product_Category_Mapping',
                    'Product.Id',
                    '=',
                    'Product_Category_Mapping.ProductId'
                )
                ->join(
                    'dbo.Category',
                    'Product_Category_Mapping.CategoryId',
                    '=',
                    'Category.Id'
                )
                ->leftJoin(
                    'dbo.Category as subcategories',
                    'Category.Id',
                    '=',
                    'subcategories.ParentCategoryId'
                )
                ->select('Category.Id', 'Category.Name')
                ->where('Supplier.Id', $id)
                ->whereNull('subcategories.Id')
                ->orderBy('Category.Name', 'asc')
                ->distinct()
                ->get();
            return $this->convertKeysToCamelCase($query);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function getNameById($id)
    {
        try {
            $query = DB::connection('webshopdb')
                ->table('dbo.Supplier')
                ->select('Supplier.Name')
                ->where('Supplier.Id', $id)
                ->get();

            return $this->convertKeysToCamelCase($query);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }
}
