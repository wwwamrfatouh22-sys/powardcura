<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffloginRequest;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Auth;

class StaffAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.staff_login');
    }

    public function login(StaffloginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $credentials['status'] = 'active';

        if (auth()->guard('staff')->attempt($credentials)) {
            $request->session()->regenerate();
            AuditLogger::log('login.success', null, ['role' => 'staff']);

            if (in_array(auth()->guard('staff')->user()?->role, ['radiology_lab', 'radiology', 'laboratory', 'lab'], true)) {
                return redirect()->route('staff.radiology_lab');
            }

            return redirect()->route('staff.dashboard');
        }

        return back()->withErrors([
            'email' => 'Credentials don\'t match our records',
        ]);
    }

    public function logout()
    {
        AuditLogger::log('logout', null, ['role' => 'staff']);
        Auth::guard('staff')->logout();
        return redirect('/');

    }
}
