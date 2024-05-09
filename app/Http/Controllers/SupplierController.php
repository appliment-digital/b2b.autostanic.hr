<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function getAll()
    {
        try {
            $query = DB::connection('webshopdb')
                ->table('dbo.Supplier')
                ->select('Supplier.Id', 'Supplier.Name')
                ->get();

            return $this->convertKeysToCamelCase($query);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function getAllCategoriesForSupplier($id)
    {
        try {
            $query = DB::connection('webshopdb')
                ->table('dbo.Supplier')
                ->select('Supplier.Id', 'Supplier.Name')
                ->get();

            return $this->convertKeysToCamelCase($query);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }
}
