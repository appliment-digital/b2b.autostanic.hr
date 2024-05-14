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
                    'Picture.SeoFilename',
                    'Picture.MimeType'
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
                ->orderBy('Category.Id')
                ->distinct()
                ->get()
                ->toArray();

            $response = [];

            $categoriesById = collect($query)->groupBy('Id');

            foreach ($categoriesById as $categoryId => $categories) {
                $categoryData = [
                    'Id' => $categoryId,
                    'Name' => $categories[0]->Name,
                    'ParentCategoryId' => $categories[0]->ParentCategoryId,
                    'ProductCount' => $categories[0]->ProductCount,
                    'PictureId' => $categories[0]->PictureId,
                    'Breadcrumb' => $categories[0]->Breadcrumb,
                    'DisplayOrder' => $categories[0]->DisplayOrder,
                    'SeoFilename' => $categories[0]->SeoFilename,
                    'MimeType' => $categories[0]->MimeType,
                ];

                $pictureUrl = [];

                foreach ($categories as $category) {
                    $paddedPictureId = str_pad(
                        $category->PictureId,
                        7,
                        '0',
                        STR_PAD_LEFT
                    );
                    $mimeType = $category->MimeType;
                    $extension = explode('/', $mimeType);
                    $fileExtension = end($extension);
                    $pictureUrl = "https://www.autostanic.hr/content/images/thumbs/{$paddedPictureId}_{$categories[0]->SeoFilename}_170.{$fileExtension}";
                }

                $categoryData['picture_url'] = $pictureUrl;

                $response[] = $categoryData;
            }

            return $this->convertKeysToCamelCase($response);
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
                    'Category.Id',
                    'Category.Name',
                    'Category.ParentCategoryId',
                    'Category.ProductCount',
                    'Category.PictureId',
                    'Category.Breadcrumb',
                    'Category.DisplayOrder',
                    'Picture.SeoFilename',
                    'Picture.MimeType'
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
                ->where('Category.ParentCategoryId', $id)
                ->where('Category.Deleted', 0)
                ->where('Category.Published', 1)
                ->orderBy('Category.DisplayOrder')
                ->orderBy('Category.Id')
                ->distinct()
                ->get()
                ->toArray();

            $response = [];

            $categoriesById = collect($query)->groupBy('Id');

            foreach ($categoriesById as $categoryId => $categories) {
                $categoryData = [
                    'Id' => $categoryId,
                    'Name' => $categories[0]->Name,
                    'ParentCategoryId' => $categories[0]->ParentCategoryId,
                    'ProductCount' => $categories[0]->ProductCount,
                    'PictureId' => $categories[0]->PictureId,
                    'Breadcrumb' => $categories[0]->Breadcrumb,
                    'DisplayOrder' => $categories[0]->DisplayOrder,
                    'SeoFilename' => $categories[0]->SeoFilename,
                    'MimeType' => $categories[0]->MimeType,
                ];

                $pictureUrl = null;

                foreach ($categories as $category) {
                    $paddedPictureId = str_pad(
                        $category->PictureId,
                        7,
                        '0',
                        STR_PAD_LEFT
                    );
                    $mimeType = $category->MimeType;
                    $extension = explode('/', $mimeType);
                    $fileExtension = end($extension);

                    if ($mimeType) {
                        $pictureUrl = "https://www.autostanic.hr/content/images/thumbs/{$paddedPictureId}_{$categories[0]->SeoFilename}_170.{$fileExtension}";
                    } else {
                        $pictureUrl =
                            'https://www.autostanic.hr/content/images/default-image.png';
                    }
                }

                $categoryData['picture_url'] = $pictureUrl;

                $response[] = $categoryData;
            }

            return $this->convertKeysToCamelCase($response);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }
}
