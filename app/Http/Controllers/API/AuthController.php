<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    //新規登録
    public function register(Request $request)
    {
        # code...
        // バリデーション
        $validateData = $request->validate([
            'name'  => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);

        //パスワードbcryptを使ってハッシュ化
        $validateData['password'] = bcrypt($request->password);
        $user = User::create($validateData);

        //  アクセストーク発行
        $accessToken = $user->createToken('authToken')->accessToken;
        return response([
            'user' => $user,
            'accessToken' => $accessToken
        ]);
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email'    => 'email|required',
            'password' => 'required'
        ]);

        //認証が正しくできなかった場合
        if (!auth()->attempt($loginData)) {
            return response([
                'message' => 'Invalid Credentials'
            ]);
        }

        // アクセストークン発行
        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response([
            'user' => auth()->user(),
            'access_token' => $accessToken
        ]);
    }
}
