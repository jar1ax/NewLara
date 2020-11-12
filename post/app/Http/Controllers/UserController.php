<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{


    public function register(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

//        $tokenObject=$user->createToken('Personal Access Token');
//        $tokenString= $tokenObject->accessToken;
//        return response()->json($tokenString,201);
        $token = $user->createToken('AuthToken')->accessToken;
        return response()->json([
//            'user'=>$user,
            'token' => $token],
            201);
//        return response()->json($user);

    }
}
