<?php

namespace App\Services;

use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use phpDocumentor\Reflection\Types\Boolean;

class UserService
{
    public function createUser(array $data): User
    {
        return User::create($data);
    }
    public function updateUser(array $data,int $id): void
    {
        User::where('id',$id)->update($data);
    }
}
