<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Proveedor;
//jwt
 use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Hash;  
//jwt2
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    
    public function login(Request $request)
    {

        try {

            $request->validate([
                'correo' => 'required|string|email',
                'password' => 'required|string',
            ]);
            //Send failed response if request is not valid
            $credentials = $request->only('correo', 'password');
            $token = JWTAuth::attempt($credentials);//creat token
            //$token = Auth::attempt($credentials);
            if (!$token) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                ], 401);
            } 
            $user = Auth::user();

            return response()->json([
                    'status' => 'success',
                    'user' => $user,
                    'authorisation' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
            ]);  


        } catch (\Throwable $th) {
            return response()->json(["message"=>$th->getMessage(),'data'=>null],400);
        }
        catch (JWTException $th) {
            return response()->json(["message"=>$th->getMessage(),'data'=>null],400);
        }

    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }
    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
