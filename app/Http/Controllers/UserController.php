<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu!');
        }
        $search = $request->search;

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%$search%")
                        ->orWhere('username', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                        ->orWhere('role', 'like', "%$search%")
                        ->orWhere('no_telp', 'like', "%$search%");
        })->paginate(10)->withQueryString();

        return view('owner.users', compact('users'));
    }

    public function create()
    {
        return view('owner.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email',
            'no_telp' => [
                'required',
                'regex:/^0[0-9]{10,}$/'
            ],
            'password' => 'required|min:6|confirmed',
            'role' => 'required'
        ], [
            'no_telp.regex' => 'Nomor harus angka, minimal 11 digit, diawali 0',
            'password.confirmed' => 'Konfirmasi password tidak sama'
        ]);

        \App\Models\User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'no_telp' => $request->no_telp,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'status' => 'active'
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan');
    }
    public function update(Request $request)
    {
        $user = User::find($request->id);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'no_telp' => $request->no_telp,
            'role' => $request->role,
            'status' => $request->status
        ]);

        return back()->with('success', 'User berhasil diupdate');
    }
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'password' => bcrypt('123456')
        ]);

        return back()->with('success', 'Password berhasil direset menjadi 123456');
    }
}
