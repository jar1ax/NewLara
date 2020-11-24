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
    public function updateUser(array $data)
    {
           return User::where('id',$data['id'])->update($data);
    }
}
