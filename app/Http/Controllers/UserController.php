<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;
use Illuminate\Support\Facades\Mail;


class UserController extends BaseController
{

    public function get($id)
    {
        try{

            return User::find($id);

        }catch (Exception $e) {
            return response()->json(['error' => 'Exception: ' . $e->getMessage()]);
        }
    }

    public function getAllActive()
    {
        try{

            //ovdje treba dohvatiti sve koji su aktivni imaju role customer i to sa vezom na tablicu kategorija popusta


        }catch (Exception $e) {
            return response()->json(['error' => 'Exception: ' . $e->getMessage()]);
        }
    }
    
    public function create(Request $request)
    {
        try{

            return User::add($request);

        }catch (Exception $e) {
            return response()->json(['error' => 'Exception: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try{

            return User::updateUser($request, $id);

        }catch (Exception $e) {
            return response()->json(['error' => 'Exception: ' . $e->getMessage()]);
        }
    }

    public function deactivate($id)
    {
        try{

            return User::deactivate($id);

        }catch (Exception $e) {
            return response()->json(['error' => 'Exception: ' . $e->getMessage()]);
        }
    }

}
