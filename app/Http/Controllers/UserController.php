<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $modules = [
            'inventory_view' => 'Inventory',
            'products' => 'Products',
            'add_stock' => 'Add Stock',
            'print_barcodes' => 'Print Barcodes',
            'suppliers' => 'Suppliers',
            'purchases' => 'Purchases',
            'expenses' => 'Expenses',
            'inventory_logs' => 'Inventory Logs',
            'customers' => 'Customers',
        ];
        return view('users.create', compact('roles', 'modules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        $permissions = [];
        foreach ($request->input('permissions', []) as $perm => $value) {
            $permissions[$perm] = true;
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'permissions' => $permissions,
        ]);

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $modules = [
            'inventory_view' => 'Inventory',
            'products' => 'Products',
            'add_stock' => 'Add Stock',
            'print_barcodes' => 'Print Barcodes',
            'suppliers' => 'Suppliers',
            'purchases' => 'Purchases',
            'expenses' => 'Expenses',
            'inventory_logs' => 'Inventory Logs',
            'customers' => 'Customers',
        ];
        return view('users.edit', compact('user', 'roles', 'modules'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        $permissions = [];
        foreach ($request->input('permissions', []) as $perm => $value) {
            $permissions[$perm] = true;
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'permissions' => $permissions,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
