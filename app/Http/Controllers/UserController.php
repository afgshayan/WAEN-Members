<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // ---------------------------------------------------------------------------
    // Index — list all users
    // ---------------------------------------------------------------------------

    public function index()
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $users = User::orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    // ---------------------------------------------------------------------------
    // Create form
    // ---------------------------------------------------------------------------

    public function create()
    {
        if (!auth()->user()->isAdmin()) abort(403);

        return view('users.create');
    }

    // ---------------------------------------------------------------------------
    // Store — save new user
    // ---------------------------------------------------------------------------

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|max:191|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            'role'                  => 'required|in:admin,editor,viewer',
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User "' . $data['name'] . '" created successfully.');
    }

    // ---------------------------------------------------------------------------
    // Edit form
    // ---------------------------------------------------------------------------

    public function edit(User $user)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        return view('users.edit', compact('user'));
    }

    // ---------------------------------------------------------------------------
    // Update — save changes to user
    // ---------------------------------------------------------------------------

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $rules = [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:191|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,editor,viewer',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $data = $request->validate($rules);

        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->role  = $data['role'];

        if ($request->filled('password')) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    // ---------------------------------------------------------------------------
    // Destroy — delete user (cannot delete own account)
    // ---------------------------------------------------------------------------

    public function destroy(User $user)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
