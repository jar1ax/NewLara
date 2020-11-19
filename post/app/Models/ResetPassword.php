<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use App\Mail\ResetPasswordMail;

class ResetPassword extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'user_id',
        'token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];
//    public function SendResetPassword()
//    {
//        $this->mail
//    }
    public function user()
    {
        $this->belongsTo('App\Models\User');
    }
}
