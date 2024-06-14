<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\SuppliersDetail;
use App\Models\DiscountType;
use App\Models\ProductSearch;
use App\Models\Warrent;
use App\Models\DeliveryDeadline;
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
                ->where('Product.Price', '>', 0)
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
                    'Product.ShortDescription',
                    'Product.FullDescription',
                    'Product.Price',
                    'Product.IsNewPart',
                    'Product.IsUsedPart',
                    'Product.ManufacturerName',
                    'Product.SupplierId',
                    'Product_Category_Mapping.CategoryId',
                    'Category.Name as CategoryName',
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
                ->join(
                    'Category',
                    'Product_Category_Mapping.CategoryId',
                    '=',
                    'Category.Id'
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
                ->where('Product.Price', '>', 0)
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
                    // Fetch supplier details for category
                    $supplierDetails = SuppliersDetail::where(
                        'web_db_supplier_id',
                        $product->SupplierId
                    )
                        ->where('web_db_category_id', $product->CategoryId)
                        ->whereNull('min_product_cost')
                        ->whereNull('max_product_cost')
                        ->first();

                    // Calculate the adjusted price
                    if ($supplierDetails) {
                        $markup = $supplierDetails->mark_up ?? 0;
                        $expenses = $supplierDetails->expenses ?? 0;
                        $adjustedPrice =
                            $product->Price +
                            $product->Price * ($markup / 100) +
                            $expenses;
                        $product->Price = $adjustedPrice;
                    } else {
                        // Fetch supplier details with price range
                        $supplierDetailsWithPriceRange = SuppliersDetail::where(
                            'web_db_supplier_id',
                            $product->SupplierId
                        )
                            ->where('web_db_category_id', $product->CategoryId)
                            ->where(function ($query) use ($product) {
                                $query
                                    ->where(
                                        'min_product_cost',
                                        '<=',
                                        $product->Price
                                    )
                                    ->where(
                                        'max_product_cost',
                                        '>=',
                                        $product->Price
                                    );
                            })
                            ->first();

                        // Calculate the adjusted price if supplier details with price range exist
                        if ($supplierDetailsWithPriceRange) {
                            $markup =
                                $supplierDetailsWithPriceRange->mark_up ?? 0;
                            $expenses =
                                $supplierDetailsWithPriceRange->expenses ?? 0;
                            $adjustedPrice =
                                $product->Price +
                                $product->Price * ($markup / 100) +
                                $expenses;
                            $product->Price = $adjustedPrice;
                        }
                    }

                    // Generate picture URL
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

                    // Fetch discount for the current user
                    $authUser = Auth::user();
                    $discountPercentage =
                        DiscountType::getDiscountForUser(
                            $authUser->discount_type_id
                        ) ?? 0;
                    $product->discountPercentage = $discountPercentage;
                    // Calculate price with discount
                    $product->PriceWithDiscount =
                        $product->Price -
                        $product->Price * ($discountPercentage / 100);

                    $product->PriceWithDiscount = $product->PriceWithDiscount;

                    $product->PriceString = number_format(
                        $product->Price,
                        2,
                        ',',
                        '.'
                    );
                    $product->PriceWithDiscountString = number_format(
                        $product->PriceWithDiscount,
                        2,
                        ',',
                        '.'
                    );

                    $product->Price = $product->Price;

                    return $product;
                })
                ->toArray();

            // Fetch categories details
            $categories = collect();
            $categoryIdsToFetch = [$productsQuery[0]->CategoryId];

            while (!empty($categoryIdsToFetch)) {
                $fetchedCategories = DB::connection('webshopdb')
                    ->table('dbo.Category')
                    ->select(
                        'Category.Id',
                        'Category.Name',
                        'Category.ParentCategoryId'
                    )
                    ->whereIn('Category.Id', $categoryIdsToFetch)
                    ->get();

                $categories = $categories->merge($fetchedCategories);

                $categoryIdsToFetch = $fetchedCategories
                    ->pluck('ParentCategoryId')
                    ->filter(function ($value) {
                        return !is_null($value);
                    })
                    ->unique()
                    ->diff($categories->pluck('Id'))
                    ->all();
            }

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

            $categoryName = isset($productsQuery[0])
                ? $productsQuery[0]->CategoryName
                : '';

            $response = [
                'products' => $this->convertKeysToCamelCase($productsQuery),
                'status' => $this->convertKeysToCamelCase($statusesQuery),
                'manufacturers' => $manufacturersQuery->pluck(
                    'ManufacturerName'
                ),
                'productCount' => $productCount,
                'categoryName' => $categoryName,
                'categories' => $categories,
            ];

            return $this->convertKeysToCamelCase($response);
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

    public function getProductById($id)
    {
        try {
            // Fetch the product data
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
                    'Product.IsNewPart',
                    'Product.IsUsedPart',
                    'Product.ManufacturerName',
                    'Product.SupplierId',
                    'Product_Category_Mapping.CategoryId'
                )
                ->join(
                    'Product_Category_Mapping',
                    'Product.Id',
                    '=',
                    'Product_Category_Mapping.ProductId'
                )
                ->where('Product.Id', $id)
                ->first();

            if ($productData) {
                // Fetch supplier details for category
                $supplierDetails = SuppliersDetail::where(
                    'web_db_supplier_id',
                    $productData->SupplierId
                )
                    ->where('web_db_category_id', $productData->CategoryId)
                    ->whereNull('min_product_cost')
                    ->whereNull('max_product_cost')
                    ->first();

                // Calculate the adjusted price
                if ($supplierDetails) {
                    $markup = $supplierDetails->mark_up ?? 0;
                    $expenses = $supplierDetails->expenses ?? 0;
                    $adjustedPrice =
                        $productData->Price +
                        $productData->Price * ($markup / 100) +
                        $expenses;
                    $productData->Price = $adjustedPrice;

                    $warrent =
                        Warrent::find($supplierDetails->warrent_id)
                            ->description ?? 'prvi';
                    $productData->warrent = $warrent;

                    $deliveryDeadline =
                        DeliveryDeadline::find(
                            $supplierDetails->delivery_deadline_id
                        )->description ?? 'prvi';
                    $productData->deliveryDeadline = $deliveryDeadline;
                } else {
                    // Fetch supplier details with price range
                    $supplierDetailsWithPriceRange = SuppliersDetail::where(
                        'web_db_supplier_id',
                        $productData->SupplierId
                    )
                        ->where('web_db_category_id', $productData->CategoryId)
                        ->where(function ($query) use ($productData) {
                            $query
                                ->where(
                                    'min_product_cost',
                                    '<=',
                                    $productData->Price
                                )
                                ->where(
                                    'max_product_cost',
                                    '>=',
                                    $productData->Price
                                );
                        })
                        ->first();

                    // Calculate the adjusted price if supplier details with price range exist
                    if ($supplierDetailsWithPriceRange) {
                        $markup = $supplierDetailsWithPriceRange->mark_up ?? 0;
                        $expenses =
                            $supplierDetailsWithPriceRange->expenses ?? 0;
                        $adjustedPrice =
                            $productData->Price +
                            $productData->Price * ($markup / 100) +
                            $expenses;
                        $productData->Price = $adjustedPrice;

                        $warrent =
                            Warrent::find(
                                $supplierDetailsWithPriceRange->warrent_id
                            )->description ?? '';
                        $productData->warrent = $warrent;

                        $deliveryDeadline =
                            DeliveryDeadline::find(
                                $supplierDetailsWithPriceRange->delivery_deadline_id
                            )->description ?? '';
                        $productData->deliveryDeadline = $deliveryDeadline;
                    }
                }

                $authUser = Auth::user();
                $discountPercentage =
                    DiscountType::getDiscountForUser(
                        $authUser->discount_type_id
                    ) ?? 0;
                $productData->discountPercentage = $discountPercentage;
                // Calculate price with discount
                $productData->PriceWithDiscount =
                    $productData->Price -
                    $productData->Price * ($discountPercentage / 100);

                $productData->PriceWithDiscount =
                    $productData->PriceWithDiscount;

                $productData->PriceString = number_format(
                    $productData->Price,
                    2,
                    ',',
                    '.'
                );
                $productData->PriceWithDiscountString = number_format(
                    $productData->PriceWithDiscount,
                    2,
                    ',',
                    '.'
                );

                $productData->Price = $productData->Price;
            }

            $productData->Price = round($productData->Price, 2);
            $productData->PriceString = number_format(
                $productData->Price,
                2,
                ',',
                '.'
            );

            // Fetch categories details
            $categories = collect();
            $categoryIdsToFetch = [$productData->CategoryId];

            while (!empty($categoryIdsToFetch)) {
                $fetchedCategories = DB::connection('webshopdb')
                    ->table('dbo.Category')
                    ->select(
                        'Category.Id',
                        'Category.Name',
                        'Category.ParentCategoryId'
                    )
                    ->whereIn('Category.Id', $categoryIdsToFetch)
                    ->get();

                $categories = $categories->merge($fetchedCategories);

                $categoryIdsToFetch = $fetchedCategories
                    ->pluck('ParentCategoryId')
                    ->filter(function ($value) {
                        return !is_null($value);
                    })
                    ->unique()
                    ->diff($categories->pluck('Id'))
                    ->all();
            }

            $productData->Categories = $categories;

            return $productData;
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error fetching records: ' . $e->getMessage());
        }
    }

    /**
     * Helper function to recursively add subcategories.
     */
    public function addSubcategories(
        &$organizedCategories,
        $categoriesById,
        $parentCategoryId
    ) {
        foreach ($categoriesById as $category) {
            if ($category->ParentCategoryId == $parentCategoryId) {
                $organizedCategories[] = $category;
                $this->addSubcategories(
                    $organizedCategories,
                    $categoriesById,
                    $category->Id
                );
            }
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
            ->where('Product_OEMCode_Mapping.IsOEMCode', 1)
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

            if ($item->CarModelYearFrom && $item->CarModelYearTO) {
                $item->CarModelYearFromTo =
                    explode('/', $item->CarModelYearFrom)[1] .
                    '-' .
                    explode('/', $item->CarModelYearTO)[1];
            } else {
                $item->CarModelYearFromTo = '-';
            }

            // remove unused fields
            unset($item->CarModelYearFrom);
            unset($item->CarTypeId);
            unset($item->CarModelId);
            unset($item->CarModelYearTO);

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
            $count = DB::connection('webshopdb')
                ->table('dbo.Product as p')
                ->leftJoin(
                    'dbo.Product_Category_Mapping',
                    'p.Id',
                    '=',
                    'Product_Category_Mapping.ProductId'
                )
                ->where('p.SupplierId', $request->supplierId)
                ->where(
                    'Product_Category_Mapping.CategoryId',
                    $request->categoryId
                )
                ->where('p.ProductCost', '>', $request->minPrice)
                ->where('p.ProductCost', '<', $request->maxPrice)
                ->where('p.Deleted', 0)
                ->where('p.Published', 1)
                ->where('Product_Category_Mapping.Deleted', 0)
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

    public function getProductsByCodeAndTerm(
        $page,
        $pageSize,
        $code,
        $term,
        Request $filters
    ) {
        try {
            $offset = ($page - 1) * $pageSize;

            $term = preg_replace('/[^\w\s]/u', '', $term);

            $productCount = DB::connection('webshopdb')
                ->table('dbo.Product')
                ->join(
                    'Product_Category_Mapping',
                    'Product.Id',
                    '=',
                    'Product_Category_Mapping.ProductId'
                )
                ->where('Product.Deleted', 0)
                ->where('Product.Published', 1)
                ->where('Product.Price', '>', 0)
                ->where('Product_Category_Mapping.Deleted', 0)
                // codes
                // SKU
                ->when($code == 'sku', function ($query) use ($term) {
                    return $query->where('Product.sku', '=', $term);
                })
                // OEM
                ->when($code == 'oem', function ($query) use ($term) {
                    // Assuming $term contains the OEM code you want to filter by
                    return $query
                        ->join(
                            'Product_OEMCode_Mapping',
                            'Product.Id',
                            '=',
                            'Product_OEMCode_Mapping.ProductId'
                        )
                        ->where(
                            'Product_OEMCode_Mapping.OEMCodeDenormalized',
                            $term
                        );
                })
                //engineCode or gearboxCode
                ->when(
                    $code == 'EngineCode' || $code == 'GearboxCode',
                    function ($query) use ($code, $term) {
                        return $query
                            ->join(
                                'B2B.ProductSearch',
                                'Product.Id',
                                '=',
                                'ProductSearch.ProductId'
                            )
                            ->where('ProductSearch.EntityName', $code)
                            ->where('ProductSearch.EntityValue', $term);
                    }
                )
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
                ->distinct('Product.Id')
                ->count('Product.Id');

            $distinctProductIds = DB::connection('webshopdb')
                ->table('dbo.Product')
                ->select('Product.Id')
                ->join(
                    'Product_Category_Mapping',
                    'Product.Id',
                    '=',
                    'Product_Category_Mapping.ProductId'
                )
                ->where('Product.Deleted', 0)
                ->where('Product.Published', 1)
                ->where('Product.Price', '>', 0)
                ->where('Product_Category_Mapping.Deleted', 0)
                //SKU
                ->when($code == 'sku', function ($query) use ($term) {
                    return $query->where('Product.sku', $term);
                })
                //OEM
                ->when($code == 'oem', function ($query) use ($term) {
                    // Assuming $term contains the OEM code you want to filter by
                    return $query
                        ->join(
                            'Product_OEMCode_Mapping',
                            'Product.Id',
                            '=',
                            'Product_OEMCode_Mapping.ProductId'
                        )
                        ->where(
                            'Product_OEMCode_Mapping.OEMCodeDenormalized',
                            $term
                        );
                })
                //engineCode or gearboxCode
                ->when(
                    $code == 'EngineCode' || $code == 'GearboxCode',
                    function ($query) use ($code, $term) {
                        return $query
                            ->join(
                                'B2B.ProductSearch',
                                'Product.Id',
                                '=',
                                'ProductSearch.ProductId'
                            )
                            ->where('ProductSearch.EntityName', $code)
                            ->where('ProductSearch.EntityValue', $term);
                    }
                )
                //filteri
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
                ->distinct()
                ->pluck('Product.Id');

            $rankedProducts = DB::connection('webshopdb')
                ->table('Product')
                ->select([
                    'Product.Id',
                    'Product.Name',
                    'Product.Sku',
                    'Product.StockQuantity',
                    'Product.ShortDescription',
                    'Product.FullDescription',
                    'Product.Price',
                    'Product.IsNewPart',
                    'Product.IsUsedPart',
                    'Product.ManufacturerName',
                    'Product.SupplierId',
                    'Product_Category_Mapping.CategoryId',
                    'Picture.SeoFilename',
                    'Picture.MimeType',
                    'Product_Picture_Mapping.PictureId',
                    DB::raw(
                        'ROW_NUMBER() OVER (PARTITION BY Product.Id ORDER BY Product_Category_Mapping.CategoryId) AS RowNum'
                    ),
                ])
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
                        ->whereRaw(
                            'Product_Picture_Mapping.Id = (SELECT MIN(Id) FROM Product_Picture_Mapping WHERE ProductId = Product.Id)'
                        );
                })
                ->leftJoin(
                    'Picture',
                    'Product_Picture_Mapping.PictureId',
                    '=',
                    'Picture.Id'
                )
                ->whereIn('Product.Id', $distinctProductIds);

            // Bind the 'RowNum' parameter to the subquery
            $rankedProducts->addBinding([], 'select');

            $productsQuery = DB::connection('webshopdb')
                ->table(
                    DB::raw("({$rankedProducts->toSql()}) as RankedProducts")
                )
                ->mergeBindings($rankedProducts)
                ->select([
                    'Id',
                    'Name',
                    'Sku',
                    'StockQuantity',
                    'ShortDescription',
                    'FullDescription',
                    'Price',
                    'IsNewPart',
                    'IsUsedPart',
                    'ManufacturerName',
                    'SupplierId',
                    'CategoryId',
                    'SeoFilename',
                    'MimeType',
                    'PictureId',
                ])
                ->where('RowNum', 1)
                ->orderBy('IsNewPart', 'DESC')
                ->orderBy('Price', 'ASC')
                ->offset($offset)
                ->limit($pageSize)
                ->get()
                ->map(function ($product) {
                    // Fetch supplier details
                    $supplierDetails = SuppliersDetail::where(
                        'web_db_supplier_id',
                        $product->SupplierId
                    )
                        ->where('web_db_category_id', $product->CategoryId)
                        ->select('id', 'mark_up', 'expenses')
                        ->when(!is_null($product->Price), function (
                            $query
                        ) use ($product) {
                            return $query
                                ->where(
                                    'min_product_cost',
                                    '<=',
                                    $product->Price
                                )
                                ->where(
                                    'max_product_cost',
                                    '>=',
                                    $product->Price
                                );
                        })
                        ->first();

                    // Calculate new price with mark up and expenses
                    if ($supplierDetails) {
                        $markup = $supplierDetails->mark_up ?? 0;
                        $expenses = $supplierDetails->expenses ?? 0;
                        $product->Price =
                            $product->Price +
                            $product->Price * ($markup / 100) +
                            $expenses;
                    }

                    // Generate picture URL
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

                    // Fetch discount for the current user
                    $authUser = Auth::user();
                    $discountPercentage =
                        DiscountType::getDiscountForUser(
                            $authUser->discount_type_id
                        ) ?? 0;

                    $product->discountPercentage = $discountPercentage;
                    // Calculate price with discount
                    $product->PriceWithDiscount =
                        $product->Price -
                        $product->Price * ($discountPercentage / 100);
                    $product->PriceWithDiscount = $product->PriceWithDiscount;

                    $product->PriceString = number_format(
                        $product->Price,
                        2,
                        ',',
                        '.'
                    );
                    $product->PriceWithDiscountString = number_format(
                        $product->PriceWithDiscount,
                        2,
                        ',',
                        '.'
                    );

                    $product->Price = $product->Price;

                    return $product;
                })
                ->toArray();

            $manufacturersQuery = DB::connection('webshopdb')
                ->table('dbo.Product')
                ->select('ManufacturerName')
                ->where('Product.Deleted', 0)
                ->where('Product.Published', 1)
                // codes
                //SKU
                ->when($code == 'sku', function ($query) use ($term) {
                    return $query->where('Product.sku', $term);
                })
                //OEM
                ->when($code == 'oem', function ($query) use ($term) {
                    // Assuming $term contains the OEM code you want to filter by
                    return $query
                        ->join(
                            'Product_OEMCode_Mapping',
                            'Product.Id',
                            '=',
                            'Product_OEMCode_Mapping.ProductId'
                        )
                        ->where(
                            'Product_OEMCode_Mapping.OEMCodeDenormalized',
                            $term
                        );
                })
                //engineCode or gearboxCode
                ->when(
                    $code == 'EngineCode' || $code == 'GearboxCode',
                    function ($query) use ($code, $term) {
                        return $query
                            ->join(
                                'B2B.ProductSearch',
                                'Product.Id',
                                '=',
                                'ProductSearch.ProductId'
                            )
                            ->where('ProductSearch.EntityName', $code)
                            ->where('ProductSearch.EntityValue', $term);
                    }
                )
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
                // codes
                // SKU
                ->when($code == 'sku', function ($query) use ($term) {
                    return $query->where('Product.sku', $term);
                })
                ->where('Product.Deleted', 0)
                ->where('Product.Published', 1)
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

    public function getProductsByIds($page, $pageSize, $ids, Request $filters)
    {
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
                ->whereIn('Product.Id', $ids)
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
                ->distinct('Product.Id')
                ->count('Product.Id');

            $rankedProducts = DB::connection('webshopdb')
                ->table('Product')
                ->select([
                    'Product.Id',
                    'Product.Name',
                    'Product.Sku',
                    'Product.StockQuantity',
                    'Product.ShortDescription',
                    'Product.FullDescription',
                    'Product.Price',
                    'Product.IsNewPart',
                    'Product.IsUsedPart',
                    'Product.ManufacturerName',
                    'Product.SupplierId',
                    'Product_Category_Mapping.CategoryId',
                    'Picture.SeoFilename',
                    'Picture.MimeType',
                    'Product_Picture_Mapping.PictureId',
                    DB::raw(
                        'ROW_NUMBER() OVER (PARTITION BY Product.Id ORDER BY Product_Category_Mapping.CategoryId) AS RowNum'
                    ),
                ])
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
                        ->whereRaw(
                            'Product_Picture_Mapping.Id = (SELECT MIN(Id) FROM Product_Picture_Mapping WHERE ProductId = Product.Id)'
                        );
                })
                ->leftJoin(
                    'Picture',
                    'Product_Picture_Mapping.PictureId',
                    '=',
                    'Picture.Id'
                )
                ->whereIn('Product.Id', $ids);
            // Bind the 'RowNum' parameter to the subquery
            $rankedProducts->addBinding([], 'select');

            $productsQuery = DB::connection('webshopdb')
                ->table(
                    DB::raw("({$rankedProducts->toSql()}) as RankedProducts")
                )
                ->mergeBindings($rankedProducts)
                ->select([
                    'Id',
                    'Name',
                    'Sku',
                    'StockQuantity',
                    'ShortDescription',
                    'FullDescription',
                    'Price',
                    'IsNewPart',
                    'IsUsedPart',
                    'ManufacturerName',
                    'SupplierId',
                    'CategoryId',
                    'SeoFilename',
                    'MimeType',
                    'PictureId',
                ])
                ->where('RowNum', 1)
                ->orderBy('IsNewPart', 'DESC')
                ->orderBy('Price', 'ASC')
                ->offset($offset)
                ->limit($pageSize)
                ->get()
                ->map(function ($product) {
                    // Fetch supplier details
                    $supplierDetails = SuppliersDetail::where(
                        'web_db_supplier_id',
                        $product->SupplierId
                    )
                        ->where('web_db_category_id', $product->CategoryId)
                        ->select('id', 'mark_up', 'expenses')
                        ->when(!is_null($product->Price), function (
                            $query
                        ) use ($product) {
                            return $query
                                ->where(
                                    'min_product_cost',
                                    '<=',
                                    $product->Price
                                )
                                ->where(
                                    'max_product_cost',
                                    '>=',
                                    $product->Price
                                );
                        })
                        ->first();

                    // Calculate new price with mark up and expenses
                    if ($supplierDetails) {
                        $markup = $supplierDetails->mark_up ?? 0;
                        $expenses = $supplierDetails->expenses ?? 0;
                        $product->Price =
                            $product->Price +
                            $product->Price * ($markup / 100) +
                            $expenses;
                    }

                    // Generate picture URL
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

                    // Fetch discount for the current user
                    $authUser = Auth::user();
                    $discountPercentage =
                        DiscountType::getDiscountForUser(
                            $authUser->discount_type_id
                        ) ?? 0;

                    $product->discountPercentage = $discountPercentage;
                    // Calculate price with discount
                    $product->PriceWithDiscount =
                        $product->Price -
                        $product->Price * ($discountPercentage / 100);
                    $product->PriceWithDiscount = $product->PriceWithDiscount;

                    $product->PriceString = number_format(
                        $product->Price,
                        2,
                        ',',
                        '.'
                    );
                    $product->PriceWithDiscountString = number_format(
                        $product->PriceWithDiscount,
                        2,
                        ',',
                        '.'
                    );

                    $product->Price = $product->Price;

                    return $product;
                })
                ->toArray();

            $manufacturersQuery = DB::connection('webshopdb')
                ->table('dbo.Product')
                ->select('ManufacturerName')
                ->whereIn('Product.Id', $ids)
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
                ->whereIn('Product.Id', $ids)
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
        } catch (\Exception $e) {
            // Handle exceptions
            return redirect()
                ->back()
                ->with('error', 'Error fetching records: ' . $e->getMessage());
        }
    }

    public function searchProductsByTerm(
        $page,
        $pageSize,
        $term,
        Request $filters
    ) {
        $productIds = ProductSearch::searchByTerm($term);

        $products = $this->getProductsByIds(
            $page,
            $pageSize,
            $productIds,
            $filters
        );

        return response()->json($products);
    }
}
