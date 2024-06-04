<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\DiscountType;

class OrderController extends BaseController
{
    private function getBitrixCountryId($countryName)
    {
        $countries = Http::asForm()->post(
            'https://sustav.autostanic.hr//bitrix/services/main/ajax.php?mode=class&c=app%3Aapp.enums&action=getCountriesList'
        );

        foreach ($countries['data'] as $country) {
            if ($country['NAME'] === $countryName) {
                return $country['ID'];
            }
        }
    }

    public function createOrder(Request $request)
    {
        $currentUserData = auth()->user();

        $authUser = Auth::user();
        $discountPercentage =
            DiscountType::getDiscountForUser($authUser->id) ?? 0;

        $orderData = [
            'token' => env('CREATE_DEAL_PROTECTION'),
            'name' => $currentUserData->name,
            'last_name' => $currentUserData->last_name,
            'email' => $currentUserData->email,
            'company_id' => $currentUserData->bitrix_company_id,
            'address' => $currentUserData->address,
            'postal_code' => $currentUserData->postal_code,
            'city' => $currentUserData->city,
            'state_province' => $currentUserData->state_province,
            'country' => $currentUserData->country_bitrix_id,
            'state_province' => $currentUserData->state_province,
            'method_of_payment' => $currentUserData->payment_method_e_racuni,
            'customer_discount' => $discountPercentage,
            'order_total' => $request->orderTotal,
            'order_items' => $request->items,
        ];

        return Http::asForm()->post(
            'https://sustav.autostanic.hr//bitrix/services/main/ajax.php?mode=class&c=app%3Aapp.b2b.webshop&action=createOrder',
            ['orderData' => $orderData]
        );
    }
}
