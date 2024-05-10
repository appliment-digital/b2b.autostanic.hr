<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;

use App\Models\SuppliersDetail;

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

    public function getAllSuppliersWithDetails()
    {
        try {
            return SuppliersDetail::get();
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
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
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
