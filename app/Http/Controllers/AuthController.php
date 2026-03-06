<?php

namespace App\Http\Controllers;
use App\Http\Requests\doctorLoginRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {
    public function showRegistrationForm() {
        return view('auth.register_patient');
    }

    public function register(RegisterRequest $request) {

        $data = $request->validated();
        $patient = Patient::create([
            'national_id' => $data['national_id'],
            'full_name'   => $data['full_name'],
            'dob'         => $data['dob'],
            'phone'       => $data['phone'],
            'password'    => Hash::make($data['password']),
        ]);
        return redirect('/login')->with('success', 'Account created successfully!');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('national_id', 'password');

        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'Credentials don\'t match our records',
        ]);
    }
    public function selectLoginType()
    {
        return view('auth.select_login_type');
    }

    public function showDoctorLogin()
    {
        return view('auth.doctor_login');
    }

    public function doctorLogin(doctorLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->guard('doctor')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('doctor.profile');
        }

        return back()->withErrors([
            'email' => 'Credentials don\'t match our records',
        ]);
    }
    public function logout()
    {
        auth()->logout();
        return redirect('/');

    }
}
