<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; 
use App\User;


class UserController extends Controller
{
    
    public function store(Request $request)
    {

    	$this->validate($request, [
		    'email' => 'required|email',
		    'password' => 'required|min:8',
		    'password_confirmation' => 'required_with:password|same:password|min:8',
		    'name' => 'required',
		]);

        $post = User::create($this->register($request->all()));

        return response()->json($post, 201);
    }

    public function login(Request $request)
    {
    	$this->validate($request, [
		    'email' => 'required|email',
		    'password' => 'required|min:8',
		]);
    	
     	$user = User::where('email', '=', $request->input('email'))->first();

     	if(!$user || !Hash::check($request->input('password'), $user->password))
     	{
     		return response()->json(['message' => 'User does not exist', 'error'=>true]);
     	
     	}else{
     		$data = [
     			'token' => Str::random(80),
     			'token_type' => 'bearer',
     			'expire_at' => date('Y-m-d H:i:s', strtotime("+1 day"))
     		];

     		$user->api_token = $data['token'];
     		$user->save();
     		return response()->json($data, 200);
     	}

        
    }

   	public function logout(Request $request)
   	{
   		
   		$user = Auth::guard('api')->user();

	    if ($user) {
	        $user->api_token = null;
	        $user->save();
	        return response()->json(['Message' => 'User logged out.'], 200);
	    }else{
	    	return response()->json(['message' => 'User does not exist', 'error'=>true]);
	    }

	    

   	}

    private function register(array $data)
    {
    	return [
	        'name' => $data['name'],
	        'email' => $data['email'],
	        'password' => Hash::make($data['password']),
	        'api_token' => Str::random(80),
    	];
    }
   	
}
