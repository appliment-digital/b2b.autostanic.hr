<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\DiscountType;
use Exception;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;
use Illuminate\Support\Facades\Mail;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        if (
            Auth::attempt([
                'email' => $request->email,
                'password' => $request->password,
            ])
        ) {
            $authUser = Auth::user();

            if ($authUser->active == false) {
                return response()->json([
                    'data' => [
                        'error' => 'Nemate pravo pristupa.',
                    ],
                ]);
            }

            $authUserWithRoles = $authUser->load('roles');

            $discount = DiscountType::getDiscountForUser($authUser->id);

            $success['token'] = $authUser->createToken(
                'MyAuthApp'
            )->plainTextToken;
            $success['id'] = $authUser->id;
            $success['name'] = $authUser->name;
            $success['last_name'] = $authUser->last_name;
            $success['email'] = $authUser->email;
            $success['role'] = $authUserWithRoles->roles;
            $success['discount'] = $discount ?? 0;

            return $this->sendResponse($success, 'Dobro došli');
        } else {
            return response()->json([
                'data' => [
                    'error' =>
                        'Email ili lozinka koju ste unijeli nisu ispravni.',
                ],
            ]);
        }
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $id = (int) explode('|', $request->token)[0];
        if (auth()->user()->tokens()->where('id', $id)->delete()) {
            return ['message' => 'Logged out'];
        }
    }

    public function getUserName(Request $request)
    {
        try {
            return auth()->user()->name;
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function getRoles(Request $request)
    {
        try {
            return response()->json([
                'data' => auth()
                    ->user()
                    ->roles->map->only(['name']),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(
                    [
                        'error' =>
                            'E-mail koji ste unjeli ne postoji u našoj bazi.',
                    ],
                    200
                );
            } else {
                $random = rand(111111, 999999);

                $user->verification_code = $random;
                if ($user->save()) {
                    $userData = [
                        'email' => $user->email,
                        'full_name' => $user->name . ' ' . $user->last_name,
                        'random' => $random,
                    ];

                    Mail::send('emails.reset_password', $userData, function (
                        $message
                    ) use ($userData) {
                        $message->from(
                            'sales@autostanic.hr',
                            'B2B Auto Stanić'
                        );

                        $message->to(
                            $userData['email'],
                            $userData['full_name']
                        );
                        $message->subject('Zahtjev za resetiranje lozinke');
                    });

                    return response()->json(
                        [
                            'message' =>
                                'je poslan verifikacijski kod na vašu e-mail adresu',
                            'status_code' => 200,
                        ],
                        200
                    );
                } else {
                    return response()->json(
                        [
                            'error' =>
                                'Došlo je do greške, molim vas pokušajte ponovo.',
                        ],
                        200
                    );
                }
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' =>
                        'Došlo je do pogreške pri slanju emaila: ' .
                        $e->getMessage(),
                ],
                200
            );
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $user = User::where('email', $request->email)
                ->where('verification_code', $request->verification_code)
                ->first();
            if (!$user) {
                return response()->json(
                    [
                        'error' =>
                            'E-mail ili verifikacijski kod nisu ispravni.',
                    ],
                    200
                );
            } else {
                $user->password = bcrypt(trim($request->password));
                $user->verification_code = null;

                if ($user->save()) {
                    return response()->json(
                        [
                            'message' => 'resetirana lozinka.',
                            'status_code' => 200,
                        ],
                        200
                    );
                } else {
                    return response()->json(
                        [
                            'error' =>
                                'Došlo je do greške, molim vas pokušajte ponovo.',
                        ],
                        200
                    );
                }
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' =>
                        'Došlo je do pogreške pri slanju emaila: ' .
                        $e->getMessage(),
                ],
                200
            );
        }
    }
}
