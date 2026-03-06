<?php

namespace App\Http\Controllers;

use App\Http\Requests\NurseloginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NurseAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.nurse_login');
    }
    public function login(NurseloginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->guard('nurse')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('nurse.dashboard');
        }

        return back()->withErrors([
            'email' => 'Credentials don\'t match our records',
        ]);
    }
    public function logout()
    {
        Auth::guard('nurse')->logout();
        return redirect('/');

    }
}
