<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        if ($user = Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = Auth::user();
            if ($user->role == "user") {
                $token = $user->createToken('login', ['role:user'], now()->addDays(2))->plainTextToken;
                return response()->json(['status' => true, 'message' => 'Kullanıcı girişi başarılı!', 'user' => $user, 'token' => $token], 200);
            } else {
                $token = $user->createToken('login', ['role:admin'], now()->addDays(2))->plainTextToken;
                return response()->json(['status' => true, 'message' => 'Kullanıcı girişi başarılı!', 'user' => $user, 'token' => $token], 200);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Email veya şifre hatalı!'], 401);
        }
    }
}
