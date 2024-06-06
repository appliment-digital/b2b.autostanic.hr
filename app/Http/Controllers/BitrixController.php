<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class BitrixController extends Controller
{
    public function getCountriesList()
    {
        try {
            $countries = Http::asForm()->post(
                'https://sustav.autostanic.hr//bitrix/services/main/ajax.php?mode=class&c=app%3Aapp.enums&action=getCountriesList'
            );

            if ($countries['status'] === 'success' && $countries['data'] > 0) {
                return $countries['data'];
            }
        } catch (\Exception $e) {
            return [
                'exception' =>
                    $e->getMessage() .
                    ' on line ' .
                    $e->getLine() .
                    ' in file ' .
                    $e->getFile(),
            ];
        }
    }

    public function sendQuery(Request $request)
    {
        try {
            $currentUserData = auth()->user();

            $queryData = [
                'token' => env('CREATE_LEAD_PROTECTION'),
                'name' => $request->name,
                'last_name' => $request->last_name,
                'full_name' => $request->name . ' ' . $request->last_name,
                'email' => $request->email,
                'company_id' => $currentUserData->bitrix_company_id,
                'address' => $currentUserData->address,
                'postal_code' => $currentUserData->postal_code,
                'city' => $currentUserData->city,
                'state_province' => $currentUserData->state_province,
                'country_name' => $currentUserData->country,
                'country' => $currentUserData->country_bitrix_id,
                'message' => $request->message,
            ];

            $response = Http::asForm()->post(
                'https://sustav.autostanic.hr//bitrix/services/main/ajax.php?mode=class&c=app%3Aapp.b2b.webshop&action=createQuery',
                ['queryData' => $queryData]
            );

            Mail::send('emails.query', $queryData, function ($message) use (
                $queryData
            ) {
                $message->from('sales@autostanic.hr', 'B2B Auto Stanić');
                $message->to($queryData['email'], $queryData['full_name']);
                $message->subject('Hvala na upitu');
            });

            return $response['data'];
        } catch (\Exception $e) {
            return [
                'exception' =>
                    $e->getMessage() .
                    ' on line ' .
                    $e->getLine() .
                    ' in file ' .
                    $e->getFile(),
            ];
        }
    }
}
