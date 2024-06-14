<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuppliersDetail;
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
                ->orderBy('Supplier.Name', 'asc')
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
            // Fetch the categories
            $categories = DB::connection('webshopdb')
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

            // Convert keys to camel case
            $categories = $this->convertKeysToCamelCase($categories);

            // Fetch the SuppliersDetail records for the given supplier ID
            $suppliersDetails = SuppliersDetail::where(
                'web_db_supplier_id',
                $id
            )
                ->whereNull('min_product_cost')
                ->whereNull('max_product_cost')
                ->get();

            // Create a list of category IDs present in SuppliersDetail
            $suppliersDetailCategoryIds = $suppliersDetails
                ->pluck('web_db_category_id')
                ->toArray();

            // Add the disabled property to each category
            foreach ($categories as &$category) {
                if (is_array($category)) {
                    $category['disabled'] = in_array(
                        $category['id'],
                        $suppliersDetailCategoryIds
                    );
                }
            }

            return $categories;
        } catch (Exception $e) {
            return response()->json([
                'error' =>
                    'Exception: ' .
                    $e->getMessage() .
                    ' on line ' .
                    $e->getLine() .
                    ' in file ' .
                    $e->getFile(),
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
