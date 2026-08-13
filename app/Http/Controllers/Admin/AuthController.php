<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function login()
    {
        return view('admin.login');
    }

    public function proses(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'Email atau password salah.');
    }

    public function register()
    {
        return view('admin.register');
    }

    public function prosesRegister(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'admin_staf',
        ]);

        Auth::login($user);

        return redirect()->route('admin.dashboard')->with('success', 'Akun berhasil dibuat, selamat datang!');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }

    // ===== Lupa Password (admin/staf) =====
    public function formLupaPassword()
    {
        return view('admin.lupa_password');
    }

    public function kirimLinkReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker()->sendResetLink($request->only('email'));

        // Pesan generik (tidak konfirmasi/tolak keberadaan email) demi keamanan.
        return back()->with('success', 'Jika email terdaftar, tautan reset password telah dikirim. Silakan cek inbox (atau folder spam).');
    }

    public function formResetPassword(Request $request, string $token)
    {
        return view('admin.reset_password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function prosesResetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('admin.login')->with('success', 'Password berhasil diganti, silakan login dengan password baru.');
        }

        return back()->with('error', 'Link reset tidak valid atau sudah kedaluwarsa. Silakan minta link baru.');
    }
}
