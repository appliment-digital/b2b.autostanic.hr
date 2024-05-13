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
        Request $filters
    ) {
        try {
            $offset = ($page - 1) * $pageSize;

            $productCount = DB::connection('webshopdb')
                ->table('dbo.Product')
                ->join(
                    'Product_Category_Mapping',
                    'Product.Id',
                    '=',
                    'Product_Category_Mapping.ProductId'
                )
                ->where('Product_Category_Mapping.CategoryId', $categoryId)
                ->where('Product.Deleted', 0)
                ->where('Product.Published', 1)
                ->where('Product_Category_Mapping.Deleted', 0)
                //filters
                ->when($filters->has('ManufacturerName'), function (
                    $query
                ) use ($filters) {
                    return $query->whereIn(
                        'Product.ManufacturerName',
                        $filters->ManufacturerName
                    );
                })
                ->when($filters->has('IsUsedPart'), function ($query) use (
                    $filters
                ) {
                    return $query->where(
                        'Product.IsUsedPart',
                        $filters->IsUsedPart
                    );
                })
                ->when($filters->has('IsNewPart'), function ($query) use (
                    $filters
                ) {
                    return $query->where(
                        'Product.IsNewPart',
                        $filters->IsNewPart
                    );
                })
                ->count();

            $query = DB::connection('webshopdb')
                ->table('dbo.Product')
                ->select(
                    'Product.Id',
                    'Product.Name',
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
                ->where('Product_Picture_Mapping.DisplayOrder', 1)
                //filters
                ->when($filters->has('ManufacturerName'), function (
                    $query
                ) use ($filters) {
                    return $query->whereIn(
                        'Product.ManufacturerName',
                        $filters->ManufacturerName
                    );
                })
                ->when($filters->has('IsUsedPart'), function ($query) use (
                    $filters
                ) {
                    return $query->where(
                        'Product.IsUsedPart',
                        $filters->IsUsedPart
                    );
                })
                ->when($filters->has('IsNewPart'), function ($query) use (
                    $filters
                ) {
                    return $query->where(
                        'Product.IsNewPart',
                        $filters->IsNewPart
                    );
                })
                ->where('Product.Deleted', 0)
                ->where('Product.Published', 1)
                ->where('Product_Category_Mapping.Deleted', 0)
                ->groupBy(
                    'Product.Id',
                    'Product.Name',
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
                ->orderBy('Product.Price', 'asc')
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
                ->orderBy('Product.ManufacturerName', 'asc')
                ->groupBy('ManufacturerName');

            $response = [
                'products' => [],
                'manufacturers' => $manufacturersQuery->pluck(
                    'ManufacturerName'
                ),
                'productCount' => $productCount,
            ];

            $productsById = collect($query)->groupBy('Id');

            foreach ($productsById as $productId => $products) {
                $productData = [
                    'id' => $productId,
                    'name' => $products[0]->Name,
                    'sku' => $products[0]->Sku,
                    'stockQuantity' => $products[0]->StockQuantity,
                    'price' => $products[0]->Price,
                    'oldPrice' => $products[0]->OldPrice,
                    'isNewPart' => $products[0]->IsNewPart,
                    'isUsedPart' => $products[0]->IsUsedPart,
                    'manufacturerName' => $products[0]->ManufacturerName,
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
                            'pictureUrl'
                        ] = "https://www.autostanic.hr/content/images/thumbs/{$paddedPictureId}_{$products[0]->SeoFilename}_280.{$fileExtension}";
                    } else {
                        $productData['pictureUrl'][] =
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
            $picture->url = "https://www.autostanic.hr/content/images/thumbs/{$pictureId}_{$picture->SeoFilename}_550.{$fileExtension}";
            // https://www.autostanic.hr/content/images/thumbs/{$pictureId}_{$picture->SeoFilename}_100{$fileExtension}.jpg
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

    public function getProductsBySupplierCategoresAndPriceRange(
        Request $request
    ) {
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
                ->select('Product.Id', 'Product.ProductCost')
                ->where('Supplier.Id', $request->supplierId)
                ->where('Category.Id', $request->categoryIds)
                ->whereBetween('Product.ProductCost', [
                    $request->minPrice,
                    $request->maxPrice,
                ])
                ->get();

            return $query;
        } catch (Exception $e) {
            return [
                'exception' =>
                    $e->getMessage() .
                    ' on line ' .
                    $e->getLine() .
                    ' in file ' .
                    $e->getFile(),
            ];
        }
    }
}
