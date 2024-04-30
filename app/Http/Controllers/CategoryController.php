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
                    'Category.Id',
                    'Category.Name',
                    'Category.ParentCategoryId',
                    'Category.ProductCount',
                    'Category.PictureId',
                    'Category.Breadcrumb',
                    'Category.DisplayOrder',
                    'Picture.SeoFilename'
                )
                ->leftJoin(
                    'dbo.Category_Picture_Mapping',
                    'Category_Picture_Mapping.CategoryId',
                    '=',
                    'Category.Id'
                )
                ->leftJoin(
                    'dbo.Picture',
                    'Category_Picture_Mapping.PictureId',
                    '=',
                    'Picture.Id'
                )
                ->where('Category.ParentCategoryId', 0)
                ->where('Category.ShowOnHomePage', 1)
                ->where('Category.Deleted', 0)
                ->where('Category.Published', 1)
                ->orderBy('Category.DisplayOrder')
                ->get();

            $categoryDataWithPictureUrls = [];

            $categoriesById = $query->groupBy('Id');

            foreach ($categoriesById as $categoryId => $categories) {
                $categoryData = [];

                $categoryData['id'] = $categories[0]->Id;
                $categoryData['name'] = $categories[0]->Name;

                $categoryPictureUrls = [];

                foreach ($categories as $category) {
                    $paddedPictureId = str_pad(
                        $category->PictureId,
                        7,
                        '0',
                        STR_PAD_LEFT
                    );
                    $categoryPictureUrls[] = "https://www.autostanic.hr/content/images/thumbs/{$paddedPictureId}_{$category->SeoFilename}_170.jpg";
                }

                $categoryData['picture_urls'] = $categoryPictureUrls;

                $categoryDataWithPictureUrls[$categoryId] = $categoryData;
            }

            return $this->convertKeysToCamelCase($categoryDataWithPictureUrls);
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
                ->whereRaw('LOWER(REPLACE(Name, "-", " ")) = ?', [
                    strtolower(str_replace('-', ' ', $name)),
                ])

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

    public function test()
    {
    }
}
