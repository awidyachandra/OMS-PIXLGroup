<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helpers\LogHelper;

class AuthController extends Controller
{
     public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
{
    $credentials = $request->only('username', 'password');
    
    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        // ❌ CEK STATUS (TAMBAHKAN DI SINI)
        if ($user->status == 'inactive') {
            Auth::logout();
            return back()->with('error', 'Akun Anda nonaktif');
        }
        LogHelper::add('info', 'User login', 'User: ' . $user->username);
        
        // redirect berdasarkan role
        switch ($user->role) {
            case 'owner':
                return redirect('/owner/dashboard');
            case 'marketing':
                return redirect('/marketing/dashboard');
            case 'storage':
                return redirect('/storage/dashboard');
            case 'finance':
    return redirect('finance/dashboard');

case 'supervisor':
    return redirect('/calendar');

case 'creative and design':
    return redirect('/calendar');
        }
    }


    return back()->with('error', 'Username atau password salah');
}
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'new_password.confirmed' => 'Konfirmasi password tidak sama'
        ]);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // cek password lama
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->with('error', 'Password lama salah');
        }

        // update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);
        LogHelper::add('info', 'Change Password', 'User: ' . $user->username);
        return back()->with('success', 'Password berhasil diubah');
    }

    public function logout()
{
    $user = Auth::user(); // ✅ ambil user dulu

    LogHelper::add(
        'info',
        'User logout',
        'User: ' . ($user->username ?? 'unknown')
    );

    Auth::logout();

    return redirect('/');
}
}
