<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\DeliveryDeadline;

class DeliveryDeadlineController extends BaseController
{
    public function getAll()
    {
        try {
            $deliveryDeadlines = DeliveryDeadline::get();

            return response()->json(['data' => $deliveryDeadlines]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }
}
