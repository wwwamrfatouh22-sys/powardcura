<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\AuditLogger;

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
            AuditLogger::log('login.success', null, ['role' => 'admin']);

            return redirect()->route('admin.dashboard');
        }

        return back()->with('error','Invalid login');

    }
    public function logout()
    {

        AuditLogger::log('logout', null, ['role' => 'admin']);
        Auth::guard('admin')->logout();

        return redirect()->route('admin.login');

    }

}
