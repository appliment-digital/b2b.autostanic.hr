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
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;

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

    private function generateRandomPassword($length = 12)
    {
        $characters =
            '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()-_';
        $password = '';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, $charactersLength - 1)];
        }
        return $password;
    }

    public function add(Request $request)
    {
        try {
            $password = $this->generateRandomPassword();

            $request->request->add(['password' => Hash::make($password)]);

            $user = User::add($request);

            if (!isset($user['id'])) {
                return $this->sendError(
                    $user,
                    'Greška prilikom dodavanja korisnika.'
                );
            }

            $userData = [
                'email' => $user->email,
                'full_name' => $user->name . ' ' . $user->last_name,
                'password' => $password,
            ];

            Mail::send('emails.access_data', $userData, function (
                $message
            ) use ($userData) {
                $message->from('sales@autostanic.hr', 'Pristupni podaci');
                $message->to($userData['email'], $userData['full_name']);
                $message->subject('Pristupni podaci');
            });

            $success['user'] = $user;

            return $this->sendResponse(
                $success,
                'dodan korisnik i poslani pristupni podaci.'
            );
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
