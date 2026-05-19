<?php

namespace App\Http\Controllers;
use App\Http\Requests\doctorLoginRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Patient;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
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
            'gender'      => $data['gender'],
            'phone'       => $data['phone'],
            'password'    => Hash::make($data['password']),
        ]);

        $patient->forceFill([
            'file_number' => 'PAT' . str_pad((string) $patient->id, 6, '0', STR_PAD_LEFT),
        ])->save();

        return redirect()->route('patient.login')->with('success', 'Account created successfully!');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('national_id', 'password');

        if (Auth::guard('patient')->attempt($credentials)) {
            $patient = Auth::guard('patient')->user();

            if ($patient instanceof Patient && Hash::needsRehash((string) $patient->password)) {
                $patient->forceFill([
                    'password' => Hash::make($credentials['password']),
                ])->save();
            }

            return $this->completePatientLogin($request);
        }

        $patient = Patient::query()
            ->where('national_id', $credentials['national_id'])
            ->first();

        $storedPassword = $patient?->password !== null ? (string) $patient->password : null;
        $storedPasswordInfo = $storedPassword !== null ? password_get_info($storedPassword) : ['algo' => 0];
        $storedPasswordIsHash = ($storedPasswordInfo['algo'] ?? 0) !== 0;

        $passwordMatches = $storedPassword !== null
            && (
                ($storedPasswordIsHash && Hash::check($credentials['password'], $storedPassword))
                || (!$storedPasswordIsHash && hash_equals($storedPassword, (string) $credentials['password']))
            );

        if ($passwordMatches) {
            $patient->forceFill([
                'password' => Hash::make($credentials['password']),
            ])->save();

            Auth::guard('patient')->login($patient);

            return $this->completePatientLogin($request);
        }

        return back()->withErrors([
            'national_id' => 'Credentials don\'t match our records',
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
            AuditLogger::log('login.success', null, ['role' => 'doctor']);
            return redirect()->route('doctor.profile');
        }

        return back()->withErrors([
            'email' => 'Credentials don\'t match our records',
        ]);
    }
    public function logout()
    {
        AuditLogger::log('logout', null, ['role' => 'patient']);
        Auth::guard('patient')->logout();

        return redirect('/');

    }

    private function completePatientLogin(LoginRequest $request): RedirectResponse
    {
        $request->session()->regenerate();
        AuditLogger::log('login.success', null, ['role' => 'patient']);

        return redirect('/');
    }
}
