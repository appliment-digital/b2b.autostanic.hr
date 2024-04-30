<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductController extends BaseController
{
    public function getProductsByCategoryId($categoryId, $page)
    {
        try {
            $pageSize = 10;

            $offset = ($page - 1) * $pageSize;

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
                    'Product.ManufacturerName',
                    'Picture.SeoFilename',
                    'Picture.MimeType',
                    'Picture.Id as PictureId'
                )
                ->join(
                    'Product_Category_Mapping',
                    'Product.Id',
                    '=',
                    'Product_Category_Mapping.ProductId'
                )
                ->leftJoin(
                    'dbo.Product_Picture_Mapping',
                    'Product_Picture_Mapping.ProductId',
                    '=',
                    'Product.Id'
                )
                ->leftJoin(
                    'dbo.Picture',
                    'Product_Picture_Mapping.PictureId',
                    '=',
                    'Picture.Id'
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
                    'Product.ManufacturerName',
                    'Picture.SeoFilename',
                    'Picture.MimeType',
                    'Picture.Id'
                )
                ->offset($offset)
                ->limit($pageSize)
                ->get();

            // Format Picture URLs
            foreach ($query as $product) {
                $pictureUrls = [];
                if (!is_null($product->PictureId)) {
                    $fileExtension = explode('/', $product->MimeType)[1];
                    $pictureIdPadded = str_pad(
                        $product->PictureId,
                        7,
                        '0',
                        STR_PAD_LEFT
                    );
                    $pictureUrl = "https://www.autostanic.hr/content/images/thumbs/{$pictureIdPadded}_{$product->SeoFilename}_280.{$fileExtension}";
                    $pictureUrls[] = $pictureUrl;
                } else {
                    $pictureUrl =
                        'https://www.autostanic.hr/content/images/thumbs/default-image_280.png';
                    $pictureUrls[] = $pictureUrl;
                }
                $product->pictureUrls = $pictureUrls;
            }

            return $this->convertKeysToCamelCase($query);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function getProductById($id)
    {
        try {
            $id = 1982906;

            $productData = DB::connection('webshopdb')
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
                ->where('Product.Id', $id)
                ->get();

            //return $productData;

            $productPictures = $this->getProductPictures($id);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error fetching records: ' . $e->getMessage());
        }
    }

    public function getProductPictures($id)
    {
        $id = 44038;
        $productPictures = DB::connection('webshopdb')
            ->table('Product_Picture_Mapping')
            ->select('Picture.*')
            ->join(
                'Picture',
                'Product_Picture_Mapping.PictureId',
                '=',
                'Picture.Id'
            )
            ->join(
                'Product',
                'Product_Picture_Mapping.ProductId',
                '=',
                'Product.Id'
            )
            ->where('Product_Picture_Mapping.ProductId', $id)
            ->get();

        return $productPictures;
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

    public function test()
    {
        $id = 1982906;

        return $this->getProductPictures($id);
    }
}
