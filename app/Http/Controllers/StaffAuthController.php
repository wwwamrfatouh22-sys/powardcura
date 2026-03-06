<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffloginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.staff_login');
    }

    public function login(StaffloginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if (auth()->guard('staff')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('staff.leave.index');
        }

        return back()->withErrors([
            'email' => 'Credentials don\'t match our records',
        ]);
    }

    public function logout()
    {
        Auth::guard('staff')->logout();
        return redirect('/');

    }
}
