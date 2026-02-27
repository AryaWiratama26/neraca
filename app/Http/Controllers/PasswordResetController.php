<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\User;

class PasswordResetController extends Controller
{
    public function requestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendEmail(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Kami tidak dapat menemukan pengguna dengan alamat email tersebut.']);
        }

        $token = Password::createToken($user);

        return redirect()->route('password.reset', ['token' => $token])->with('email', $request->email)->with('status', 'Link reset password berhasil dimuat. (Mode demo: otomatis dialihkan)');
    }

    public function resetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'request' => $request,
            'token' => $token,
            'email' => $request->session()->get('email', $request->email)
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->save();
        });

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Password telah berhasil direset! Silakan masuk dengan password baru.')
            : back()->withErrors(['email' => __($status)]);
    }
}
