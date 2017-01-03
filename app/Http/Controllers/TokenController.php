<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    public function token(Request $request, JWTAuth $auth)
    {

        if (!Auth::check()) {
            return response()->json(['error' => 'not_logged_in'], 401);
        }

        //Auth::loginUsingId($request->id);
        $user = Auth::user();

        $claims = ['userid' => $user->id, 'email' => $user->email];


        $token = $auth->fromUser($user, $claims);

        return response()->json(['token' => $token]);

    }
}
