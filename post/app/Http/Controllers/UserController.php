<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

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

    public function update(UserUpdateRequest $request,User $user)
    {
        $authUser = $request->user();

        if ($authUser->can('update',$user))
        {
            $this->userService->updateUser($request->all(),$user->id);

            return response()->json(['message' => 'User data has been updated!']);
        }

        return response()->json(['message' => 'User data hasn\'t been updated!']);
    }

    public function getAllUsers()
    {
        $users = User::get()->pluck('name','email');

        return response()->json(['Users'=>$users],200);
    }

    public function getUserData(User $user)
    {
       $authUser = \request()->user();

        if ($authUser->can('update',$user))
        {
           if (User::where('id',$user->id)->exists())
           {
               return response()->json( User::where('id',$user->id)->get(),200);
           }
           else
           {
               return response()->json(['message' => 'User data hasn\'t been found'],404);
           }
        }

        return response()->json(['message' => 'Premission denied'],403);
    }
}

