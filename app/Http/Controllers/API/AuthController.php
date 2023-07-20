<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //User Login
    public function userLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors(),'status'=>false], 400);
        }

        $user = User::where('email',$request->email)->first();

        if(! $user || ! Hash::check($request->password, $user->password ))
        {
            return response()->json(['status' => false,
             'message' => 'Invalid email and password.',
             'error' => 'Unauthorized'], 401);
        }
        else
        {
            if($user->status != '1')
            {
                return response()->json(['status' => false, 'message' => 'Sorry! Your account is blocked. Please contact the support team.', 'error' => 'Unauthorized'], 401);
            }
            else
            {
                if (! $token = auth('api')->attempt($validator->validated())) {
                    return response()->json(['status' => false, 'message' => 'Invalid email and password.', 'error' => 'Unauthorized'], 401);
                }

                return $this->createUserToken($token);
            }
        }
    }

    //Generate Fuelin User Token
    protected function createUserToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'status'=>true
        ],200);
    }

    //Customer Login
    public function customerLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors(),'status'=>false], 400);
        }

        $user = Customer::where('email',$request->email)->first();

        if(! $user || ! Hash::check($request->password, $user->password ))
        {
            return response()->json(['status' => false,
             'message' => 'Invalid email and password.',
             'error' => 'Unauthorized'], 401);
        }
        else
        {
            if($user->status != '1')
            {
                return response()->json(['status' => false, 'message' => 'Sorry! Your account is blocked. Please contact the support team.', 'error' => 'Unauthorized'], 401);
            }
            else
            {
                if (! $token = auth('api_customer')->attempt($validator->validated())) {
                    return response()->json(['status' => false, 'message' => 'Invalid email and password.', 'error' => 'Unauthorized'], 401);
                }

                return $this->createCustomerToken($token);
            }
        }
    }

    //Generate Customer Token
    protected function createCustomerToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api_customer')->factory()->getTTL() * 60,
            'status'=>true
        ],200);
    }

    public function customer_register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|regex:/^[a-z A-Z]+$/u',
            'last_name' => 'required|regex:/^[a-z A-Z]+$/u',
            'email' => 'required|email:rfc,dns|unique:customers,email,NULL,id,deleted_at,NULL',
            'contact' => 'required|digits:10|unique:customers,contact,NULL,id,deleted_at,NULL',
            'nic' => 'required|unique:customers,contact,NULL,id,deleted_at,NULL',
            'password' => 'required|min:8|same:password_confirmation|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'password_confirmation' => 'required',
        ],
        [
            'password.regex' => 'Password must contain at least one number, both uppercase and lowercase letters and special character.',
        ] );

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors(),'status'=>false], 400);
        }

        $customer = new Customer();
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->contact = $request->contact;
        $customer->email = $request->email;
        $customer->nic = $request->nic;
        $customer->status = 1;
        $customer->password = Hash::make($request->password);
        $customer->save();

        if (! $token = auth('api_customer')->attempt($validator->validated())) {
            return response()->json(['status' => false, 'message' => 'Invalid email and password.', 'error' => 'Unauthorized'], 401);
        }

        return $this->createCustomerToken($token);
    }

    public function customer_logout()
    {
        auth('api_customer')->logout();
        return response()->json(['status' => true, 'message' => 'User logged out successfully'],200);
    }

    public function customer_profile()
    {
        $customer = Customer::select('id','first_name','last_name','contact','email','nic')->find(auth('api_customer')->user()->id);

        return response()->json(['customer'=>$customer]);
    }

    public function user_logout()
    {
        auth('api')->logout();
        return response()->json(['status' => true, 'message' => 'User logged out successfully'],200);
    }

    public function user_profile()
    {
        $user = User::select('id','first_name','last_name','contact','email')->find(auth('api')->user()->id);

        return response()->json(['user'=>$user]);
    }
}
