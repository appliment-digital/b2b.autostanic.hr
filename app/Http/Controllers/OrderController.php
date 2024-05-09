<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Http;

class OrderController extends BaseController
{
    public function createOrder(Request $request)
    {
        $currentUserData = auth()->user();

        $arFields = [
            'token' => $currentUserData->payment_method,
            'name' => $currentUserData->name,
            'last_name' => $currentUserData->last_name,
            'email' => $currentUserData->email,
            'company_id' => $currentUserData->bitrix_company_id,
            'delivery_point' => $currentUserData->delivery_point,
            'payment_method' => $currentUserData->payment_method,
        ];
        return Http::asForm()->post(
            'https://sustav.autostanic.hr//bitrix/services/main/ajax.php?mode=class&c=app%3Aapp.b2b.webshop&action=createOrder',
            ['arFields' => $arFields]
        );
    }
}
