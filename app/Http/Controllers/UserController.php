<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Check authorization
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Super Admin privileges required.');
        }

        $query = User::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('region', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        // Filter by region
        if ($request->has('region') && $request->region != '') {
            $query->where('region', $request->region);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get unique regions for filter
        $regions = User::whereNotNull('region')
            ->distinct()
            ->pluck('region')
            ->sort();

        return view('users.index', compact('users', 'regions'));
    }

    public function create()
    {
        // Check authorization
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Super Admin privileges required.');
        }

        $regions = ['Bali', 'NTT', 'NTB'];
        return view('users.create', compact('regions'));
    }

    public function store(Request $request)
    {
        // Check authorization
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Super Admin privileges required.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:super_admin,admin_region,user',
            'region' => 'nullable|string|max:255|required_if:role,admin_region',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'region' => $request->region,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        // Check authorization
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Super Admin privileges required.');
        }

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Check authorization
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Super Admin privileges required.');
        }

        $regions = ['Jakarta', 'Surabaya', 'Medan', 'Bandung', 'Makassar', 'Palembang', 'Semarang', 'Denpasar'];
        return view('users.edit', compact('user', 'regions'));
    }

    public function update(Request $request, User $user)
    {
        // Check authorization
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Super Admin privileges required.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:super_admin,admin_region,user',
            'region' => 'nullable|string|max:255|required_if:role,admin_region',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'region' => $request->region,
        ];

        // Only update password if provided
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Check authorization
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Super Admin privileges required.');
        }

        // Prevent deletion of current user
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        // Prevent deletion of last super admin
        if ($user->isSuperAdmin() && User::where('role', 'super_admin')->count() <= 1) {
            return redirect()->route('users.index')->with('error', 'Cannot delete the last super admin.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        // Check authorization
        if (!auth()->user()->isSuperAdmin()) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        // Prevent deactivating current user
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'You cannot deactivate your own account.'], 400);
        }

        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $user->is_active,
            'message' => $user->is_active ? 'User activated successfully.' : 'User deactivated successfully.'
        ]);
    }
}