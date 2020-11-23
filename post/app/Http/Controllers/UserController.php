<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(Request $request)
    {
        $user= $this->userService->createUser(array_merge($request->only('name','email'),
            ['password'=>bcrypt($request->password)]));
        $token = $user->createToken('AuthToken')->accessToken;

        return response()->json(['token' => $token], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        if( \Auth::attempt(['email'=>$request->email, 'password'=>$request->password]))
        {
            $user = \Auth::user();
            $token = $user->createToken('AuthToken')->accessToken;

            return response()->json(['token' => $token], 200);
        }
    }
    public function update(UserUpdateRequest $request)
    {

        $update = $this->userService->updateUser($request);

        return $update;
    }
}

