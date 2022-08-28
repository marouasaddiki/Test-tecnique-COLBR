<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Hash;

class AuthController extends Controller
{

    // Register API 
    public function register(Request $request){
             
        // check if required fields are filled
        $validator = Validator::make($request->all() ,[
            'Prenom'=>'required|min:2|max:100',
            'nom'=>'required|min:2|max:100',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6|max:100',
            'confirm_password'=>'required|same:password'
        ]);
         // if not , send an error message
        if ($validator->fails()) {
            return response()->json([
                'message'=>'validation fails',
                'errors' => $validator->errors(),
            ],422);
        }
       //send to the table users in ddatabase
        $user = User::create([
           'Prenom'=>$request->Prenom,
           'nom'=>$request->nom,
           'email'=>$request->email,
           'password'=>Hash::make($request->password),
       ]);
    //    return success messages
       return response()->json([
        'message'=>'Registration successfull',
        'data' => $user,
    ],200);
}

    // Login API      
public function login(Request $request){
    // to log in
     $validator = $request->validate(
         [
            'email'=>'required|email',
            'password'=>'required|min:6|max:100',
         ]); 

    $login = User::where('email',$validator['email'])->first();
    // check if the email matches the data in the database
    if (! $login ) {
        return response()->json([
            'error' => 'Unauthorized'
        ], 401);
    }
    // check if the password matches the data in the database
    if(!Hash::check($validator['password'],$login->password)){
    return response()->json([
        'error' => 'Unauthorized'
    ], 401);
    }
  // generate the token and send them in response
    $token = $login->createToken('CLE_SECRETE')->plainTextToken;
    return response([
        'login' => $login,
        'token' => $token
    ],200);
}
    }

