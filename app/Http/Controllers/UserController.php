<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'password' => 'required',
            'email' => 'required|email|unique:users'
        ]);
        $user=new User();
        $user->fill($request->all());
        $user->password=Hash::make($request->password);
        $user->api_token=Str::random(60);
        $user->save();
        return response()->json($user,201);
    }
    public function login(Request $request)
    {
        $user=User::where('email',$request->email)->first();
        if(!$user){
            return response()->json('Usuario O contraseña incorrectos',401);
        }
        if(!Hash::check($request->password,$user->password)){
            return response()->json('Usuario O contraseña incorrectos',401);
        }
        return response()->json($user);
    }
}
