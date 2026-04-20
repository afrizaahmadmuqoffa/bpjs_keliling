<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->search) {
            $search = strtolower($request->search);

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nama) LIKE ?', ["%{$search}%"])
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'     => 'required|string|max:255',
            'nik'      => 'required|digits:16|unique:users,nik',
            'email'    => 'required|email|unique:users,email',
            'no_hp'    => 'nullable|string|max:20',
            'role'     => 'required|in:admin,pic',
            'password' => 'required|min:6'
        ]);

        User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Tidak bisa edit akun super_admin lain
        if ($user->role === 'super_admin') {
            return back()->withErrors(['msg' => 'Tidak bisa mengubah akun super admin.']);
        }

        $validated = $request->validate([
            'nama'  => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'role'  => 'required|in:admin,pic',
        ]);

        $user->update($validated);

        return back()->with('success', 'User berhasil diupdate');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->withErrors(['msg' => 'Tidak bisa hapus akun sendiri']);
        }

        if ($user->role === 'super_admin') {
            return back()->withErrors(['msg' => 'Tidak bisa menghapus akun super admin.']);
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus');
    }
}