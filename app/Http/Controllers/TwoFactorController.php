<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    public function setup()
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        if (!$user->google2fa_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->google2fa_secret = $secret;
            $user->save();
        } else {
            $secret = $user->google2fa_secret;
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            'Neraca',
            $user->email,
            $secret
        );

        // Generate QR SVG
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($qrCodeUrl);

        return view('auth.2fa-setup', compact('secret', 'qrCode', 'user'));
    }

    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = Auth::user();
        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->code);

        if (!$valid) {
            return back()->with('error', 'Kode OTP tidak valid. Coba lagi.');
        }

        $user->google2fa_enabled = true;
        $user->save();

        ActivityLog::log('2fa_enabled', $user, '2FA berhasil diaktifkan.');

        return redirect()->route('profile.index')->with('success', 'Two-Factor Authentication berhasil diaktifkan!');
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        $user->google2fa_enabled = false;
        $user->google2fa_secret = null;
        $user->save();

        ActivityLog::log('2fa_disabled', $user, '2FA dinonaktifkan.');

        return redirect()->route('profile.index')->with('success', 'Two-Factor Authentication dinonaktifkan.');
    }

    public function verify()
    {
        return view('auth.2fa-verify');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = Auth::user();
        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->code);

        if (!$valid) {
            return back()->with('error', 'Kode OTP tidak valid.');
        }

        session(['2fa_verified' => true]);

        return redirect()->intended('/dashboard');
    }
}
