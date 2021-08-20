<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController as ResponseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;






class UserController extends ResponseController
{
 
     

    public function register(Request $request)

    {

        $validator = Validator::make($request->all(), [

            'name' => 'required',

            'lastname' => 'required',

            'cedula' => 'required',
            
            'username' => 'required',

            'email' => 'required|email',

            'emailconfirm' => 'required',

            'password' => 'required',

        ]);


        if($validator->fails()){

            return $this->sendError('Validation Error.', $validator->errors());       

        }

   

        $input = $request->all();

        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);

        $success['token'] =  $user->createToken('Token-Autorizacion')->accessToken;

        $success['name'] =  $user->name;

   

        return $this->sendResponse($success, 'User register successfully.');

    }

    public function login(Request $request){

        $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Usuario Incorrecto']
            ]);
        }

        return $user->createToken('Token-Autorizacion')->accessToken;
     }
 
 
 
     public function showUser(){
         $user = User::all();
         return $user;
     }
 
 
}
