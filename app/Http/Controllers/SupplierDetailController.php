<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\SuppliersDetail;
use App\Models\Warrent;
use App\Models\DeliveryDeadline;

class SupplierDetailController extends BaseController
{
    public function getSupplierWithDetails($id)
    {
        try {
            return SuppliersDetail::find($id);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    private function getSuppliersName($id)
    {
        return DB::connection('webshopdb')
            ->table('dbo.Supplier')
            ->select('Supplier.Name')
            ->where('Supplier.Id', $id)
            ->pluck('Name')
            ->first();
    }

    public function getCategoryName($id)
    {
        return DB::connection('webshopdb')
            ->table('dbo.Category')
            ->select('Category.Name')
            ->where('Category.Id', $id)
            ->pluck('Name')
            ->first();
    }

    public function getAllSuppliersWithDetails()
    {
        try {
            $suppliersDetails = SuppliersDetail::orderBy(
                'updated_at',
                'desc'
            )->get();

            foreach ($suppliersDetails as &$detail) {
                $detail->supplier_name = $this->getSuppliersName(
                    $detail->web_db_supplier_id
                );
                $detail->category_name = $this->getCategoryName(
                    $detail->web_db_category_id
                );
                if ($detail->warrent_id) {
                    $detail->warrent_name = Warrent::find(
                        $detail->warrent_id
                    )->description;
                }
                if ($detail->delivery_deadline_id) {
                    $detail->delivery_deadline_name = DeliveryDeadline::find(
                        $detail->delivery_deadline_id
                    )->description;
                }
            }

            return $suppliersDetails;
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function addDetailsforSupplier(Request $request)
    {
        try {
            $suppliersDetails = SuppliersDetail::add($request);

            if (empty($suppliersDetails)) {
                return $this->sendError(
                    $suppliersDetails,
                    'Greška prilikom dodavanja detalja za dobavljača.'
                );
            }

            $success['suppliersDetails'] = $suppliersDetails;

            return $this->sendResponse(
                $success,
                'dodani detalji za dobavljača.'
            );
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

    public function updateDetailsforSupplier($id, Request $request)
    {
        try {
            $supplierDetails = SuppliersDetail::updateSuppliersDetail(
                $id,
                $request
            );

            if (empty($supplierDetails)) {
                return $this->sendError(
                    $supplierDetails,
                    'Greška prilikom uređivanja detalja za dobavljača.'
                );
            }

            $success['supplierDetails'] = $supplierDetails;

            return $this->sendResponse(
                $success,
                'uređeni detalji za dobavljača.'
            );
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function getUniqueCategories()
    {
        try {
            $uniqueCategoryIds = SuppliersDetail::distinct()->pluck(
                'web_db_category_id'
            );

            $uniqueCategories = [];

            foreach ($uniqueCategoryIds as $id) {
                $uniqueCategories[] = [
                    'id' => $id,
                    'name' => $this->getCategoryName($id),
                ];
            }

            return $uniqueCategories;
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function getAddedPriceRange(Request $request)
    {
        try {
            $supplierId = $request->supplierId;
            $categoryId = $request->categoryId;

            $categoryName = $this->getCategoryName($categoryId);

            $priceRangeData = SuppliersDetail::where(
                'web_db_supplier_id',
                $supplierId
            )
                ->where('web_db_category_id', $categoryId)
                ->select(
                    'id',
                    'web_db_category_id',
                    'min_product_cost',
                    'max_product_cost'
                )
                ->get();

            return $priceRangeData;
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }
}
