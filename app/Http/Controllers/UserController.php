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
        try {

            return User::find($id);
        } catch (Exception $e) {
            return response()->json(['error' => 'Exception: ' . $e->getMessage()]);
        }
    }

    public function getAll()
    {
        try {
            //ovdje treba dohvatiti sa vezom na tablicu kategorija popusta

            $userData = User::role('customer')
                ->orderBy('users.id', 'DESC')
                ->get();

            return response()->json(['data' => $userData]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Exception: ' . $e->getMessage()]);
        }
    }

    public function add(Request $request)
    {
        try {
            $user = User::add($request);
            if ($user) {
                $success['user'] =  $user;

                return $this->sendResponse($success, 'dodan korisnik.');
            } else {
                return $this->sendError(['error' => 'Spremanje korisnika nije uspjelo.']);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Exception: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::updateUser($request, $id);

            if ($user) {
                $success['user'] =  $user;

                return $this->sendResponse($success, 'ažuriran korisnik.');
            } else {
                return $this->sendError(['error' => 'Ažuriranje korisnika nije uspjelo.']);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Exception: ' . $e->getMessage()]);
        }
    }

    public function deactivate($id)
    {
        try {

            return User::deactivate($id);
        } catch (Exception $e) {
            return response()->json(['error' => 'Exception: ' . $e->getMessage()]);
        }
    }

    public function getCurrentUserData()
    {
        return auth()->user();
    }
}
