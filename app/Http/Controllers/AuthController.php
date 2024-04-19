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


class AuthController extends BaseController
{
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $authUser = Auth::user();

            if ($authUser->active == false) {
                return response()->json(['error' => 'Nemate pravo pristupa.'], 401);
            }

            $token =  $authUser->createToken('MyAuthApp')->plainTextToken;
            $response = [
                'success' => true,
                'message' => 'Dobro došli',
                'token' => $token,
                'id' => $authUser->id,
                'name' => $authUser->name,
                'last_name' => $authUser->last_name,
                'email' => $authUser->email,
            ];
            return response()->json($response, 200);
        } else {
            return response()->json(['error' => 'Email ili lozinka koju ste unijeli nisu ispravni.'], 401);
        }
    }


    public function logout(Request $request)
    {
        $request->session()->flush();
        $id = (int)explode('|', $request->token)[0];
        if (auth()->user()->tokens()->where('id', $id)->delete()) {
            return ['message' => 'Logged out'];
        }
    }

    public function getUserName(Request $request)
    {
        try {
            return auth()->user()->name;
        } catch (Exception $e) {
            return response()->json(['error' => 'Exception: ' . $e->getMessage()]);
        }
    }

    public function getRoles(Request $request)
    {
        try {

            return response()->json(['data' => auth()->user()->roles->map->only(['name'])]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Exception: ' . $e->getMessage()]);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email'
            ]);


            $user = User::where('email', $request->email)->first();
            if (!$user) {

                return response()->json([
                    'message' => 'Wrong email adress',
                    'status_code' => 200
                ], 200);
            } else {
                $random = rand(111111, 999999);

                $user->verification_code = $random;
                if ($user->save()) {
                    $userData = array(
                        'email' => $user->email,
                        'full_name' => $user->name . " " . $user->last_name,
                        'random' => $random
                    );
                    // return $userData;
                    Mail::send('emails.reset_password', $userData, function ($message) use ($userData) {
                        $message->from('sales@autostanic.hr', 'Resetiranje lozinke');

                        $message->to($userData['email'], $userData['full_name']);
                        $message->subject('Zahtjev za resetiranje lozinke');
                    });

                    return response()->json([
                        'message' => 'We have sent a verification code to your email address',
                        'status_code' => 200
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Some error occurred, Please try again',
                        'status_code' => 200
                    ], 200);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Some error occurred while sending the email: ' . $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }



    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'verification_code' => 'required|integer',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::where('email', $request->email)->where('verification_code', $request->verification_code)->first();
        if (!$user) {
            return response()->json([
                'error' => 'error',
                'message' => 'User not found/Invalid code',
                'status_code' => 401
            ], 401);
        } else {
            $user->password = bcrypt(trim($request->password));
            $user->verification_code = Null;

            if ($user->save()) {
                return response()->json([
                    'message' => 'Password updated successfully!',
                    'status_code' => 200
                ], 200);
            } else {
                return response()->json([
                    'error' => 'error',
                    'message' => 'Some error occurred, Please try again',
                    'status_code' => 500
                ], 500);
            }
        }
    }
}
