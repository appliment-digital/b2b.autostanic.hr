<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\Warrent;

class WarrentController extends BaseController
{
    public function getAll()
    {
        try {
            $warrents = Warrent::get();

            return response()->json(['data' => $warrents]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }
}
