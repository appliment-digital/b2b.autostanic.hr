<?php

namespace App\Http\Controllers;

use App\Models\DiscountType;
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
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function getAll()
    {
        try {
            $userData = User::orderBy('users.id', 'DESC')->get();

            return response()->json(['data' => $userData]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function getAllWithRelations()
    {
        try {
            $userData = User::with('roles', 'DiscountType')
                ->orderBy('users.id', 'DESC')
                ->get();

            return response()->json(['data' => $userData]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function add(Request $request)
    {
        try {
            $user = User::add($request);
            if ($user === 'Lozinka treba imati bar 6 znakova.') {
                return $this->sendError(['error' => $user]);
            } elseif (isset($user['error'])) {
                return $this->sendError(
                    $user,
                    'Greška prilikom dodavanja korisnika.'
                );
            }

            $success['user'] = $user;
            return $this->sendResponse($success, 'Korisnik je uspješno dodan.');
        } catch (Exception $e) {
            return response()->json([
                'error' =>
                    'Exception: ' .
                    $e->getMessage() .
                    ' on line ' .
                    $e->getLine() .
                    ' in file ' .
                    $e->getFile(),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::updateUser($id, $request);

            if ($user) {
                $success['user'] = $user;

                return $this->sendResponse($success, 'ažuriran korisnik.');
            } else {
                return $this->sendError([
                    'error' => 'Ažuriranje korisnika nije uspjelo.',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function changeStatus(Request $request)
    {
        try {
            $user = User::changeStatus($request->id, $request->active);
            if ($user->active) {
                $success['user'] = $user;
                return $this->sendResponse($success, 'aktiviran korisnik.');
            } elseif (!$user->active) {
                $success['user'] = $user;
                return $this->sendResponse($success, 'deaktiviran korisnik.');
            } else {
                return $this->sendError([
                    'error' => 'Izmjena statusa korisnika nije uspjela.',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function getCurrentUserData()
    {
        return auth()->user();
    }

    public function getRoles()
    {
        try {
            return response()->json(\Spatie\Permission\Models\Role::all());
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }
}
