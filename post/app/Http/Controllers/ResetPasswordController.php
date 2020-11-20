<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ResetPassword;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;

class ResetPasswordController extends Controller
{

    public function forgot(ForgotPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user)
        {
            return response()->json(['message' => 'Unable to find user with this email']);
        }

        $token = md5($request->email.Carbon::now());

        $resetPassword = ResetPassword::create([
                'user_id' => $user->id,
                'token' => $token
            ]);

        if ($user && $resetPassword)
        {
            Mail::to($request->email)->send(new ResetPasswordMail($token));

            return response()->json(['message' => 'We have emailed you token! ']);
        }
        return response()->json(['message' => '1Unable to find user with this email']);
    }

    public function reset(ResetPasswordRequest $request)
    {
        $resetPassword = ResetPassword::where(['token' => $request->token])->first();

        if (!$resetPassword)
        {
            return response()->json(['message' => '1This token is invalid']);
        }
        if (CArbon::parse($resetPassword->updated_at)->addMinutes(120)->isPast() or
            CArbon::parse($resetPassword->created_at)->addMinutes(120)->isPast())
        {
            $resetPassword->delete();
            return response()->json(['message' => '2This token is invalid']);
        }
        $user=User::where(['id'=>$resetPassword->user_id])->first();

        if (!$user)
        {
            return response()->json(['message' => 'We cannot find user with this user_id']);
        }

        $user->password = bcrypt($request->password);
        $user->save();
        $resetPassword->delete();

        return response()->json(['message' => 'Password changed successfully!']);
    }
}
