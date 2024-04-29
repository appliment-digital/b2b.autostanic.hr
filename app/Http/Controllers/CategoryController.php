<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
class CategoryController extends BaseController
{
    public function getMainCategories()
    {
        try {
            $query = DB::connection('webshopdb')
                ->table('dbo.Category')
                ->select(
                    'Id',
                    'Name',
                    'Description',
                    'ParentCategoryId',
                    'ProductCount',
                    'PictureId',
                    'Breadcrumb'
                )
                ->where('ParentCategoryId', 0)
                ->where('ShowOnHomePage', 1)
                ->where('Deleted', 0)
                ->where('Published', 1)
                ->orderBy('DisplayOrder')
                ->get();

            return $this->convertKeysToCamelCase($query);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function getSubcategories($id)
    {
        try {
            $query = DB::connection('webshopdb')
                ->table('dbo.Category')
                ->select(
                    'Id',
                    'Name',
                    'Description',
                    'ParentCategoryId',
                    'ProductCount',
                    'PictureId',
                    'Breadcrumb'
                )
                ->where('ParentCategoryId', $id)
                ->where('Deleted', 0)
                ->where('Published', 1)
                ->get();

            return $this->convertKeysToCamelCase($query);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function getSubcategoriesByName($name)
    {
        try {
            $query = DB::connection('webshopdb')
                ->table('dbo.Category')
                ->select(
                    'Id',
                    'Name',
                    'Description',
                    'ParentCategoryId',
                    'ProductCount',
                    'PictureId',
                    'Breadcrumb'
                )
                // ->where('Name', $name)
                ->whereRaw('LOWER(REPLACE(Name, "-", " ")) = ?', [strtolower(str_replace('-', ' ', $name))])

                ->where('ParentCategoryId', 0)
                ->where('Deleted', 0)
                ->where('Published', 1)
                ->get();

            return $this->convertKeysToCamelCase($query);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }
}
