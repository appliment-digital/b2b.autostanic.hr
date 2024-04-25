<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\DiscountType;

class DiscountTypeController extends BaseController
{
    public function getAll()
    {
        try {
            $discountTypes = DiscountType::getAll();

            return response()->json(['data' => $discountTypes]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function add(Request $request)
    {
        try {
            $discountType = DiscountType::add($request);

            if ($discountType) {
                $success['discountType'] = $discountType;

                return $this->sendResponse($success, 'dodan tip rabata.');
            } else {
                return $this->sendError([
                    'error' => 'Spremanje tipa rabata nije uspjelo.',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $discountType = DiscountType::updateDiscountType($id, $request);

            if ($discountType) {
                $success['discountType'] = $discountType;

                return $this->sendResponse($success, 'ažuriran tip rabata.');
            } else {
                return $this->sendError([
                    'error' => 'Ažuriranje tipa rabata nije uspjelo.',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $discountType = DiscountType::erase($id);

            if ($discountType) {
                $success['discountType'] = $discountType;

                return $this->sendResponse($success, 'izbrisan tip rabata.');
            } else {
                return $this->sendError([
                    'error' => 'Brisanje tipa rabata nije uspjelo.',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }
}
