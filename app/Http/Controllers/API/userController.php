<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login(Request $request): Response
    {
        $input = $request->all();
        Auth ::attempt($input);
        $user = Auth::user();
        if($user){
            $token = $user->createToken('bearer')->accessToken;
        return Response(['success'=>true,'status' => 200,'token' => $token],200);}
        else
        return Response(['success'=>false,'message'=>'Invalid email or password.'],401);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function getUserDetails(): Response
    {
        if(Auth::guard('api')->check()){
            $user = Auth::guard('api')->user();
            return Response(['success'=>true,'data' => $user],200);
        }
        return Response(['success'=>false,'message' => 'Authentication failed.'],401);
    }

    /**
     * Display the specified resource.
     */
    public function userLogout(): Response
    {
        if(Auth::guard('api')->check()){
            $accessToken = Auth::guard('api')->user()->token();

                \DB::table('oauth_refresh_tokens')
                    ->where('access_token_id', $accessToken->id)
                    ->update(['revoked' => true]);
            $accessToken->revoke();

            return Response(['success'=>true,'message' => 'Logout successfully.'],200);
        }
        return Response(['success'=>false,'message' => 'Invalid Token.'],401);
    }

   
}