<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends BaseController
{
    public function getTestProduct()
    {
        try {
            $conn = DB::connection('sqlsrv');
            $query = $conn->table('dbo.Product')->where('Id', '9977');
            $data = $query->get();

            $dataFormatted = $this->convertKeysToCamelCase($data);

            return response()->json($dataFormatted[0]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error fetching records: ' . $e->getMessage());
        }
    }
}
