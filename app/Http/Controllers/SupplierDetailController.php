<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\SuppliersDetail;
use App\Models\Warrent;
use App\Models\DeliveryDeadline;

class SupplierDetailController extends Controller
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

    private function getCategoryName($id)
    {
        return DB::connection('webshopdb')
            ->table('dbo.Category')
            ->select('Category.Name')
            ->where('Category.Id', $id)
            ->pluck('Name')
            ->first();
    }

    private function getProductName($id)
    {
        return DB::connection('webshopdb')
            ->table('dbo.Product')
            ->select('Product.Name')
            ->where('Product.Id', $id)
            ->pluck('Name')
            ->first();
    }

    public function getAllSuppliersWithDetails()
    {
        try {
            $suppliersDetails = SuppliersDetail::get();

            foreach ($suppliersDetails as &$detail) {
                $detail->supplier_name = $this->getSuppliersName(
                    $detail->web_db_supplier_id
                );
                $detail->category_name = $this->getCategoryName(
                    $detail->web_db_category_id
                );
                if ($detail->web_db_product_id) {
                    $detail->product_name = $this->getProductName(
                        $detail->web_db_product_id
                    );
                }
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
            return SuppliersDetail::add($request);
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

    public function updateDetailsforSupplier(Request $request)
    {
        try {
            return SuppliersDetail::updateSuppliersDetail(
                $request->id,
                $request
            );
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }
}
