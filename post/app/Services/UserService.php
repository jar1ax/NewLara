<?php

namespace App\Services;

use App\Http\Requests\UserUpdateRequest;
use App\Models\User;

class UserService
{
    public function createUser(array $data): User
    {
        return User::create($data);
    }
    public function updateUser(UserUpdateRequest $request)
    {
        $authenticated_user= \Auth::user();

        $user=User::findOrfail($request->id);
        if ($authenticated_user->can('update',$user))
        {
            $user->update($request->all());

            return response()->json(['message'=>'User data has been updated!']);
        }
        return response()->json(['message'=>'Error. User data hasn\'t been updated!']);
    }
}
