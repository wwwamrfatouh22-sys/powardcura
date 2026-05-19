<?php

namespace App\Http\Controllers;

use App\Support\AuditLogger;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    private const BROKERS = [
        'doctor' => 'doctors',
        'staff' => 'staff',
        'admin' => 'admins',
    ];

    public function requestForm(string $role): View
    {
        $broker = $this->brokerFor($role);

        return view('auth.password_email', ['role' => $role, 'broker' => $broker]);
    }

    public function sendLink(Request $request, string $role): RedirectResponse
    {
        $broker = $this->brokerFor($role);
        $validated = $request->validate(['email' => ['required', 'email']]);

        $status = Password::broker($broker)->sendResetLink($validated);

        AuditLogger::log('password_reset.requested', null, [
            'role' => $role,
            'email' => $validated['email'],
            'status' => $status,
        ]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetForm(Request $request, string $role, string $token): View
    {
        $this->brokerFor($role);

        return view('auth.password_reset', [
            'role' => $role,
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request, string $role): RedirectResponse
    {
        $broker = $this->brokerFor($role);

        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::broker($broker)->reset(
            $validated,
            function ($user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        AuditLogger::log('password_reset.completed', null, [
            'role' => $role,
            'email' => $validated['email'],
            'status' => $status,
        ]);

        return $status === Password::PASSWORD_RESET
            ? redirect()->route($role . '.login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    private function brokerFor(string $role): string
    {
        abort_unless(isset(self::BROKERS[$role]), 404);

        return self::BROKERS[$role];
    }
}
