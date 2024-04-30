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
                    'Picture.SeoFilename' // Add the SeoFilename column from the Picture table
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
                ->orderBy('Category.Id') // Add additional order by statement
                ->distinct()
                ->get()
                ->toArray(); // Convert the collection of objects to array

            // Initialize an empty array to store the final response
            $response = [];

            // Group categories by ID
            $categoriesById = collect($query)->groupBy('Id');

            // Iterate over categories
            foreach ($categoriesById as $categoryId => $categories) {
                // Initialize an array to store category data
                $categoryData = [
                    'Id' => $categoryId,
                    'Name' => $categories[0]->Name,
                    'ParentCategoryId' => $categories[0]->ParentCategoryId,
                    'ProductCount' => $categories[0]->ProductCount,
                    'PictureId' => $categories[0]->PictureId,
                    'Breadcrumb' => $categories[0]->Breadcrumb,
                    'DisplayOrder' => $categories[0]->DisplayOrder,
                    'SeoFilename' => $categories[0]->SeoFilename,
                ];

                // Initialize an array to store picture URLs for this category
                $pictureUrls = [];

                // Construct URLs for each picture of the category
                foreach ($categories as $category) {
                    $paddedPictureId = str_pad(
                        $category->PictureId,
                        7,
                        '0',
                        STR_PAD_LEFT
                    );
                    $pictureUrls[] = "https://www.autostanic.hr/content/images/thumbs/{$paddedPictureId}_{$category->SeoFilename}_170.jpg";
                }

                // Add picture URLs array to category data
                $categoryData['picture_urls'] = $pictureUrls;

                // Add category data to the final response
                $response[] = $categoryData;
            }

            return $this->convertKeysToCamelCase($response);
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
                'Picture.SeoFilename' // Add the SeoFilename column from the Picture table
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
            ->orderBy('Category.Id') // Add additional order by statement
            ->distinct()
            ->get()
            ->toArray(); // Convert the collection of objects to array

        // Initialize an empty array to store the final response
        $response = [];

        // Group categories by ID
        $categoriesById = collect($query)->groupBy('Id');

        // Iterate over categories
        foreach ($categoriesById as $categoryId => $categories) {
            // Initialize an array to store category data
            $categoryData = [
                'Id' => $categoryId,
                'Name' => $categories[0]->Name,
                'ParentCategoryId' => $categories[0]->ParentCategoryId,
                'ProductCount' => $categories[0]->ProductCount,
                'PictureId' => $categories[0]->PictureId,
                'Breadcrumb' => $categories[0]->Breadcrumb,
                'DisplayOrder' => $categories[0]->DisplayOrder,
                'SeoFilename' => $categories[0]->SeoFilename,
            ];

            // Initialize an array to store picture URLs for this category
            $pictureUrls = [];

            // Construct URLs for each picture of the category
            foreach ($categories as $category) {
                $paddedPictureId = str_pad(
                    $category->PictureId,
                    7,
                    '0',
                    STR_PAD_LEFT
                );
                $pictureUrls[] = "https://www.autostanic.hr/content/images/thumbs/{$paddedPictureId}_{$category->SeoFilename}_170.jpg";
            }

            // Add picture URLs array to category data
            $categoryData['picture_urls'] = $pictureUrls;

            // Add category data to the final response
            $response[] = $categoryData;
        }

        return $response;
    }
}
