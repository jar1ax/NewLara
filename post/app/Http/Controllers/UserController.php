<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Mail\DeleteUserMail;
use App\Models\User;
use App\Services\UserService;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
        $users = User::all()->pluck('email');

        return response()->json(['users' => $users],200);
    }

    public function getUserData(User $user)
    {
       $authUser = \request()->user();

        if ($authUser->can('view',$user))
        {
            return new UserResource($user);
        }

        return response()->json(['message' => 'Permission denied'],403);
    }

    public function deleteUser(User $user)
    {
        $authUser = \request()->user();

        if ($authUser->can('delete',$user))
        {
            if($user)
            {
                $authUser->status = User::INACTIVE;
                $authUser->save();

                $data = [
                    'title' => 'We are sorry you leaving'
                ];
                $pdf =  PDF::loadView('pdf.delete', $data);
//                $pdf->stream('Result.pdf');

                Mail::to($authUser->email)->send(new DeleteUserMail($pdf));

                return response()->json(['message' => 'Mail sent. Status changed successfully!'],200);
            }
            else
            {
                return response()->json(['message' => 'Error! User not found'],404);
            }
        }

        return response()->json(['message' => 'Permission denied'],403);
    }
}
