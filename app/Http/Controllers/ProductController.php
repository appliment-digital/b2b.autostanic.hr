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
                // filters
                ->when(
                    $filters->has('manufacturerName') &&
                        $filters->manufacturerName !== [],
                    function ($query) use ($filters) {
                        return $query->whereIn(
                            'Product.ManufacturerName',
                            $filters->manufacturerName
                        );
                    }
                )
                ->when(
                    $filters->has('statusId') && $filters->statusId !== [],
                    function ($query) use ($filters) {
                        return $query->whereIn(
                            'Product.ProductStatusId',
                            $filters->statusId
                        );
                    }
                )
                ->count();

            $productsQuery = DB::connection('webshopdb')
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
                    'Product_Picture_Mapping.PictureId as PictureId'
                )
                ->join(
                    'Product_Category_Mapping',
                    'Product.Id',
                    '=',
                    'Product_Category_Mapping.ProductId'
                )
                ->leftJoin('Product_Picture_Mapping', function ($join) {
                    $join
                        ->on(
                            'Product.Id',
                            '=',
                            'Product_Picture_Mapping.ProductId'
                        )
                        ->where(
                            'Product_Picture_Mapping.Id',
                            '=',
                            DB::raw(
                                '(SELECT MIN(Id) FROM Product_Picture_Mapping WHERE ProductId = Product.Id)'
                            )
                        );
                })
                ->leftJoin(
                    'Picture',
                    'Product_Picture_Mapping.PictureId',
                    '=',
                    'Picture.Id'
                )
                ->where('Product_Category_Mapping.CategoryId', $categoryId)
                ->where('Product.Deleted', 0)
                ->where('Product.Published', 1)
                ->where('Product_Category_Mapping.Deleted', 0)
                // filters
                ->when(
                    $filters->has('manufacturerName') &&
                        $filters->manufacturerName !== [],
                    function ($query) use ($filters) {
                        return $query->whereIn(
                            'Product.ManufacturerName',
                            $filters->manufacturerName
                        );
                    }
                )
                ->when(
                    $filters->has('statusId') && $filters->statusId !== [],
                    function ($query) use ($filters) {
                        return $query->whereIn(
                            'Product.ProductStatusId',
                            $filters->statusId
                        );
                    }
                )
                ->orderBy('Product.IsNewPart', 'desc')
                ->orderBy('Product.Price', 'asc')
                ->offset($offset)
                ->limit($pageSize)
                ->distinct()
                ->get()
                ->map(function ($product) {
                    $pictureUrl = '';
                    if (!is_null($product->PictureId)) {
                        $paddedPictureId = str_pad(
                            $product->PictureId,
                            7,
                            '0',
                            STR_PAD_LEFT
                        );
                        $extension = explode('/', $product->MimeType);
                        $fileExtension = end($extension);
                        $pictureUrl = "https://www.autostanic.hr/content/images/thumbs/{$paddedPictureId}_{$product->SeoFilename}_280.{$fileExtension}";
                    } else {
                        $pictureUrl =
                            'https://www.autostanic.hr/content/images/thumbs/default-image_280.png';
                    }
                    $product->pictureUrl = $pictureUrl;
                    return $product;
                })
                ->toArray();

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
                ->where('Product_Category_Mapping.Deleted', 0)
                ->orderBy('Product.ManufacturerName', 'asc')
                ->groupBy('ManufacturerName');

            // Query to get all unique product statuses for the category
            $statusesQuery = DB::connection('webshopdb')
                ->table('ProductStatus')
                ->select('ProductStatus.Id', 'ProductStatus.Name')
                ->join(
                    'Product',
                    'Product.ProductStatusId',
                    '=',
                    'ProductStatus.Id'
                )
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
                ->orderBy('ProductStatus.Name', 'asc')
                ->distinct()
                ->get()
                ->map(function ($status) {
                    return [
                        'id' => $status->Id,
                        'name' => $status->Name,
                    ];
                });

            $response = [
                'products' => $this->convertKeysToCamelCase($productsQuery),
                'status' => $this->convertKeysToCamelCase($statusesQuery),
                'manufacturers' => $manufacturersQuery->pluck(
                    'ManufacturerName'
                ),
                'productCount' => $productCount,
            ];

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
        try {
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

            $url550Array = [];
            $url100Array = [];

            foreach ($productPictures as $picture) {
                $pictureId = str_pad($picture->PictureId, 7, '0', STR_PAD_LEFT);
                $fileExtension = substr(
                    $picture->MimeType,
                    strpos($picture->MimeType, '/') + 1
                );

                $url550 = "https://www.autostanic.hr/content/images/thumbs/{$pictureId}_{$picture->SeoFilename}_550.{$fileExtension}";
                $url550Array[] = $url550;

                $url100 = "https://www.autostanic.hr/content/images/thumbs/{$pictureId}_{$picture->SeoFilename}_100.{$fileExtension}";
                $url100Array[] = $url100;
            }

            if (empty($url550Array) && empty($url100Array)) {
                $url550Array[] =
                    'https://www.autostanic.hr/content/images/default-image.png';
                $url100Array[] =
                    'https://www.autostanic.hr/content/images/default-image.png';
                return [
                    'url550' => $url550Array,
                    'url100' => $url100Array,
                ];
            }
            return [
                'url550' => $url550Array,
                'url100' => $url100Array,
            ];
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function getSpecificationAttributeForProduct($id)
    {
        $response = DB::connection('webshopdb')
            ->table('dbo.Product_SpecificationAttribute_Mapping')
            ->select(
                'SpecificationAttribute.name as specificationAttributeName',
                'SpecificationAttributeOption.name as SpecificationAttributeOptionName'
            )
            ->join(
                'SpecificationAttributeOption',
                'Product_SpecificationAttribute_Mapping.SpecificationAttributeOptionId',
                '=',
                'SpecificationAttributeOption.Id'
            )
            ->join(
                'SpecificationAttribute',
                'SpecificationAttributeOption.SpecificationAttributeId',
                '=',
                'SpecificationAttribute.Id'
            )
            ->where('Product_SpecificationAttribute_Mapping.ProductId', $id)
            ->get();

        $result = [];

        foreach ($response as $item) {
            $result[$item->specificationAttributeName] =
                $item->SpecificationAttributeOptionName;
        }

        return $result;
    }

    public function getOEMCodeForProduct($id)
    {
        $oemCodes = DB::connection('webshopdb')
            ->table('Product_OEMCode_Mapping')
            ->select('OEMCodeDenormalized', 'OEMManufacturer')
            ->join(
                'Product',
                'Product.Id',
                '=',
                'Product_OEMCode_Mapping.ProductId'
            )
            ->where('Product.Id', $id)
            ->get();

        $result = [];

        foreach ($oemCodes as $code) {
            $manufacturer = $code->OEMManufacturer;
            $oemCode = $code->OEMCodeDenormalized;

            if (!isset($result[$manufacturer])) {
                $result[$manufacturer] = [];
            }

            $result[$manufacturer][] = $oemCode;
        }

        return $result;
    }

    public function getCarTypesForProduct($id)
    {
        $response = DB::connection('webshopdb')
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
            ->where('mk.Deleted', 0)
            ->where('mk.Published', 1)
            ->where('ct.Deleted', 0)
            ->where('ct.Published', 1)
            ->where('cm.Deleted', 0)
            ->where('cm.Published', 1)
            ->get();

        $result = [];

        foreach ($response as $item) {
            $carMakeName = $item->CarMakeName;

            unset($item->CarMakeId, $item->CarMakeName); // Remove redundant fields

            $result[$carMakeName][] = $item;
        }

        return $result;
    }

    public function getProductDetails($id)
    {
        $details = [
            'oemCodes' => $this->getOEMCodeForProduct($id),
            'relatedVehicles' => $this->getCarTypesForProduct($id),
        ];
        return $details;
    }

    public function getProductsBySupplierCategoresAndPriceRange(
        Request $request
    ) {
        try {
            $query = DB::connection('webshopdb')
                ->table('dbo.Product as p')
                ->leftJoin(
                    'dbo.Product_Category_Mapping',
                    'p.Id',
                    '=',
                    'Product_Category_Mapping.ProductId'
                )
                ->where('p.SupplierId', $request->supplierId);
            // Handling conditions for categoryIds
            if (in_array(null, $request->categoryIds)) {
                // If $request->categoryIds contains null
                $query->where(function ($query) use ($request) {
                    $query
                        ->whereNotExists(function ($query) {
                            $query
                                ->select(DB::raw(1))
                                ->from('Product_Category_Mapping')
                                ->whereRaw(
                                    'Product_Category_Mapping.productId = p.Id'
                                );
                        })
                        ->orWhereIn(
                            'p.Id',
                            array_filter($request->categoryIds, 'is_numeric')
                        );
                });
            } elseif (!empty($request->categoryIds)) {
                // If $request->categoryIds contains category IDs but not null
                $query->whereIn(
                    'Product_Category_Mapping.CategoryId',
                    $request->categoryIds
                );
            }
            $count = $query
                ->where('p.ProductCost', '>', $request->minPrice)
                ->where('p.ProductCost', '<', $request->maxPrice)
                ->count();

            return $count;
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
