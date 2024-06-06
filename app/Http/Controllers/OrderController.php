<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\DiscountType;

class OrderController extends BaseController
{
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
            'full_name' =>
                $currentUserData->name . ' ' . $currentUserData->last_name,
            'email' => $currentUserData->email,
            'company_id' => $currentUserData->bitrix_company_id,
            'address' => $currentUserData->address,
            'postal_code' => $currentUserData->postal_code,
            'city' => $currentUserData->city,
            'state_province' => $currentUserData->state_province,
            'country_name' => $currentUserData->country,
            'country' => $currentUserData->country_bitrix_id,
            'state_province' => $currentUserData->state_province,
            'method_of_payment' => $currentUserData->payment_method_e_racuni,
            'customer_discount' => $discountPercentage,
            'order_total' => number_format($request->orderTotal, 2, ',', '.'),
            'order_items' => $request->items,
            'note' => $request->note,
        ];

        $response = Http::asForm()->post(
            'https://sustav.autostanic.hr//bitrix/services/main/ajax.php?mode=class&c=app%3Aapp.b2b.webshop&action=createOrder',
            ['orderData' => $orderData]
        );

        if ($response['status'] === 'success' && $response['data'] > 0) {
            $orderData['title'] = 'Narudžba broj ' . $response['data']['ID'];
        }

        Mail::send('emails.order', $orderData, function ($message) use (
            $orderData
        ) {
            $message->from('sales@autostanic.hr', 'B2B Auto Stanić');
            $message->to($orderData['email'], $orderData['full_name']);
            $message->subject($orderData['title']);
        });

        return $response['data'];
    }
}
