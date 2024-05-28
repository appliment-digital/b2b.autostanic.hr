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

        $bitrixCountryId = $this->getBitrixCountryId($currentUserData->country);

        $authUser = Auth::user();
        $discountPercentage =
            DiscountType::getDiscountForUser($authUser->id) ?? 0;

        $orderItems[] = [
            'sku' => '16.10.210', //$request['sku'],
            'price' => '2.59', //$request['priceWithDiscount'],
            'priceWithVat' => '2.59',
            'quantity' => 1,
        ];

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
            'country' => $bitrixCountryId,
            'state_province' => $currentUserData->state_province,
            'customer_discount' => $discountPercentage,
            'order_total' => '10000',
            'order_items' => $orderItems,
        ];

        return Http::asForm()->post(
            'https://sustav.autostanic.hr//bitrix/services/main/ajax.php?mode=class&c=app%3Aapp.b2b.webshop&action=createOrder',
            ['orderData' => $orderData]
        );
    }
}
