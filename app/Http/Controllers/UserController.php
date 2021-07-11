<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController as ResponseController;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Passport;




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

        $success['token'] =  $user->createToken('MyApp')->accessToken;

        $success['name'] =  $user->name;

   

        return $this->sendResponse($success, 'User register successfully.');

    }




    public function login(Request $request){
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = request(['username', 'password']);

        if (!Auth::attempt($credentials))
            return $this->sendError($credentials, "Error al Logear", 402);


        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer'
        ]);

    }



    public function showUser(){
        $user = User::get();
        return $user;
    }


    


}
