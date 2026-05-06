<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Satker;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('satker')
            ->where('role', 'admin_satker')
            ->latest()
            ->get();

        $satkers = Satker::all();

        return view('admin.users', compact('users', 'satkers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:50|unique:users,username|alpha_dash',
            'email'     => 'required|email|unique:users,email',
            'kontak'    => 'nullable|string|max:20',
            'satker_id' => 'required|exists:satkers,id',
            'password'  => 'required|string|min:8|confirmed',
        ], [
            'username.unique'    => 'Username sudah dipakai.',
            'username.alpha_dash'=> 'Username hanya boleh huruf, angka, dash, dan underscore.',
            'email.unique'       => 'Email sudah terdaftar.',
            'satker_id.required' => 'Satker wajib dipilih.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'kontak'    => $request->kontak,
            'satker_id' => $request->satker_id,
            'role'      => 'admin_satker',
            'password'  => Hash::make($request->password),
        ]);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:50|unique:users,username,' . $user->id . '|alpha_dash',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'kontak'    => 'nullable|string|max:20',
            'satker_id' => 'required|exists:satkers,id',
        ]);

        $user->update([
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'kontak'    => $request->kontak,
            'satker_id' => $request->satker_id,
        ]);

        return back()->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin_pusat') {
            return back()->with('error', 'Admin Pusat tidak bisa dihapus.');
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password user ' . $user->name . ' berhasil direset.');
    }
}