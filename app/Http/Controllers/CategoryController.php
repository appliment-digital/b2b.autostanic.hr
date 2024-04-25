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
            $query = DB::connection('webDb')
                ->table('dbo.Category')
                ->select(
                    'Id',
                    'Name',
                    'Description',
                    'ParentCategoryId',
                    'ProductCount'
                )
                ->where('ParentCategoryId', 0)
                ->get();

            return $query;
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function getSubcategories($id)
    {
        try {
            $query = DB::connection('webDb')
                ->table('dbo.Category')
                ->select(
                    'Id',
                    'Name',
                    'Description',
                    'ParentCategoryId',
                    'ProductCount'
                )
                ->where('ParentCategoryId', $id)
                ->get();

            return $query;
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }
}
