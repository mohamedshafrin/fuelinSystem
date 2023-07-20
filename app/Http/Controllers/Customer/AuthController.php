<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login_form()
    {
        return view('customers.auth.login');
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        Auth::guard('customer')->attempt(['email'=>$request->email, 'password'=>$request->password]);
        if(Auth::guard('customer')->check()){
            request()->session()->put('time_zone_default',$request->timezone);
            return redirect('customer');
        }else{
            return back()->withInput()->with(['error'=>'Invalid Username or password.']);
        }
    }

    public function register_form()
    {
        return view('customers.auth.register');
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|max:50|regex:/^[a-z A-Z]+$/u',
            'last_name' => 'required|max:50|regex:/^[a-z A-Z]+$/u',
            'email' => 'required|max:255|email:rfc,dns|unique:customers,email,NULL,id,deleted_at,NULL',
            'contact' => 'required|digits:10|unique:customers,contact,NULL,id,deleted_at,NULL',
            'nic' => ['required','max:12','min:10','unique:customers,contact,NULL,id,deleted_at,NULL','regex:/^([0-9]{9}[x|X|v|V]|[0-9]{12})$/u'],
            'password' => 'required|min:8|max:16|same:password_confirmation|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'password_confirmation' => 'required|min:8|max:16',
        ],
        [
            'password.regex' => 'Password must contain at least one number, both uppercase and lowercase letters and special character.',
        ] );

        $customer = new Customer();
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->contact = $request->contact;
        $customer->email = $request->email;
        $customer->nic = $request->nic;
        $customer->status = 1;
        $customer->password = Hash::make($request->password);
        $customer->save();

        Auth::guard('customer')->attempt(['email'=>$request->email, 'password'=>$request->password]);
        if(Auth::guard('customer')->check()){
            return redirect('/customer')->with(['success'=>'Registration completed successfully', 'name'=>$customer->name]);
        }else{
            return redirect('/');
        }
    }

    public function logout()
    {
        Auth::guard('customer')->logout();

        return redirect('/');
    }
}
