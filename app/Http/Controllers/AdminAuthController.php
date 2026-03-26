<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{

public function showLogin()
{
    return view('admin.login');
}

    public function login(Request $request)
    {

        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $credentials=$request->only('email','password');

        if(Auth::guard('admin')->attempt($credentials))
        {
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');
        }

        return back()->with('error','Invalid login');

    }
    public function logout()
    {

        Auth::guard('admin')->logout();

        return redirect()->route('admin.login');

    }

}
