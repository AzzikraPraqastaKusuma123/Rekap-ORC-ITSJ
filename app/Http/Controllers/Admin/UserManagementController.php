<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'role' => ['required', 'in:superadmin,admin,staff'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan.');
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => ['required', 'in:superadmin,admin,staff'],
        ]);

        $user = User::findOrFail($id);

        // Prevent deleting the last superadmin
        if ($user->role === 'superadmin' && User::where('role', 'superadmin')->count() <= 1 && $request->role !== 'superadmin') {
            return redirect()->back()->with('error', 'Tidak bisa mengubah role Super Administrator terakhir.');
        }

        $user->update(['role' => $request->role]);

        return redirect()->back()->with('success', 'Role user berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'superadmin' && User::where('role', 'superadmin')->count() <= 1) {
            return redirect()->back()->with('error', 'Tidak bisa menghapus Super Administrator terakhir.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }
}
