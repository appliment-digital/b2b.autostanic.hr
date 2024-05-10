<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Http;
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
}
