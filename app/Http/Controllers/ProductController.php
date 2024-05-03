<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductController extends BaseController
{
    public function getProductsByCategoryId(
        $categoryId,
        $page,
        $pageSize,
        Request $request
    ) {
        try {
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
                //filters
                ->when($request->has('ManufacturerName'), function (
                    $query
                ) use ($request) {
                    return $query->where(
                        'Product.ManufacturerName',
                        $request->ManufacturerName
                    );
                })
                ->when($request->has('IsUsedPart'), function ($query) use (
                    $request
                ) {
                    return $query->where(
                        'Product.IsUsedPart',
                        $request->IsUsedPart
                    );
                })
                ->when($request->has('IsNewPart'), function ($query) use (
                    $request
                ) {
                    return $query->where(
                        'Product.IsNewPart',
                        $request->IsNewPart
                    );
                })
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

            $manufacturersQuery = DB::connection('webshopdb')
                ->table('dbo.Product')
                ->select('ManufacturerName')
                ->join(
                    'Product_Category_Mapping',
                    'Product.Id',
                    '=',
                    'Product_Category_Mapping.ProductId'
                )
                ->where('Product_Category_Mapping.CategoryId', $categoryId)
                ->where('Product.Deleted', 0)
                ->where('Product.Published', 1)
                ->groupBy('ManufacturerName');

            $response = [
                'products' => [],
                'manufacturers' => $manufacturersQuery->pluck(
                    'ManufacturerName'
                ),
            ];

            $productsById = collect($query)->groupBy('Id');

            foreach ($productsById as $productId => $products) {
                $productData = [
                    'Id' => $productId,
                    'Name' => $products[0]->Name,
                    'ShortDescription' => $products[0]->ShortDescription,
                    'FullDescription' => $products[0]->FullDescription,
                    'Sku' => $products[0]->Sku,
                    'StockQuantity' => $products[0]->StockQuantity,
                    'Price' => $products[0]->Price,
                    'OldPrice' => $products[0]->OldPrice,
                    'IsNewPart' => $products[0]->IsNewPart,
                    'IsUsedPart' => $products[0]->IsUsedPart,
                    'ManufacturerName' => $products[0]->ManufacturerName,
                    'picture_urls' => [],
                ];

                foreach ($products as $product) {
                    if ($product->SeoFilename && $product->MimeType) {
                        $paddedPictureId = str_pad(
                            $product->PictureId,
                            7,
                            '0',
                            STR_PAD_LEFT
                        );
                        $extension = explode('/', $product->MimeType);
                        $fileExtension = end($extension);
                        $productData[
                            'picture_urls'
                        ][] = "https://www.autostanic.hr/content/images/thumbs/{$paddedPictureId}_{$product->SeoFilename}_280.{$fileExtension}";
                    } else {
                        $productData['picture_urls'][] =
                            'https://www.autostanic.hr/content/images/thumbs/default-image_280.png';
                    }
                }

                $response['products'][] = $productData;
            }

            return $this->convertKeysToCamelCase($response);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function getProductById($id)
    {
        try {
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
                ->first();

            $productData->pictures = $this->getProductPictures($id);
            $productData->oem_codes = $this->getOEMCodeForProduct($id);
            $productData->specification_attributes = $this->getSpecificationAttributeForProduct(
                $id
            );
            $productData->car_types = $this->getCarTypesForProduct($id);

            return $productData;
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error fetching records: ' . $e->getMessage());
        }
    }

    public function getProductPictures($id)
    {
        $productPictures = DB::connection('webshopdb')
            ->table('dbo.Product_Picture_Mapping')
            ->select(
                'Picture.Id as PictureId',
                'Picture.MimeType',
                'Picture.SeoFilename'
            )
            ->join(
                'Picture',
                'Product_Picture_Mapping.PictureId',
                '=',
                'Picture.Id'
            )
            ->where('Product_Picture_Mapping.ProductId', $id)
            ->get();

        // Generate URLs
        $productPictures->transform(function ($picture) {
            $pictureId = str_pad($picture->PictureId, 7, '0', STR_PAD_LEFT);
            $fileExtension = substr(
                $picture->MimeType,
                strpos($picture->MimeType, '/') + 1
            );
            $picture->url = "https://www.autostanic.hr/content/images/thumbs/{$pictureId}_{$picture->SeoFilename}_280.{$fileExtension}";
            return $picture;
        });

        return $productPictures;
    }

    public function getOEMCodeForProduct($id)
    {
        $OEMCodes = DB::connection('webshopdb')
            ->table('dbo.Product_OEMCode_Mapping')
            ->select('OEMCode.*')
            ->join(
                'OEMCode',
                'Product_OEMCode_Mapping.OEMCodeId',
                '=',
                'OEMCode.Id'
            )
            ->where('Product_OEMCode_Mapping.ProductId', $id)
            ->where('OEMCode.Published', 1)
            ->get();

        return $OEMCodes;
    }

    public function getSpecificationAttributeForProduct($id)
    {
        $specificationAttributes = DB::connection('webshopdb')
            ->table('dbo.Product_SpecificationAttribute_Mapping')
            ->select('SpecificationAttributeOption.*')
            ->join(
                'SpecificationAttributeOption',
                'Product_SpecificationAttribute_Mapping.SpecificationAttributeOptionId',
                '=',
                'SpecificationAttributeOption.Id'
            )
            ->where('Product_SpecificationAttribute_Mapping.ProductId', $id)
            ->get();

        return $specificationAttributes;
    }

    public function getCarTypesForProduct($id)
    {
        $carTypes = DB::connection('webshopdb')
            ->table('dbo.Product_CarType_Mapping as pcm')
            ->select(
                // CarType
                'ct.Id as CarTypeId',
                'ct.Name as CarTypeName',
                'ct.kW as kW',
                'ct.EngineType as EngineType',
                // CarModel
                'cm.Id as CarModelId',
                'cm.Name as CarModelName',
                'cm.ModelYearFrom as CarModelYearFrom',
                'cm.ModelYearTO as CarModelYearTO',
                'cm.CarMakeId',
                // CarMake
                'mk.Id as CarMakeId',
                'mk.Name as CarMakeName'
            )
            ->join('CarType as ct', 'pcm.CarTypeId', '=', 'ct.Id')
            ->join('CarModel as cm', 'ct.CarModelId', '=', 'cm.Id')
            ->join('CarMake as mk', 'cm.CarMakeId', '=', 'mk.Id')
            ->where('pcm.ProductId', $id)
            ->where('ct.Deleted', 0)
            ->where('ct.Published', 1)
            ->get();

        return $carTypes;
    }

    public function test()
    {
        $id = 1982906;

        return $this->getProductById($id);
    }
}
