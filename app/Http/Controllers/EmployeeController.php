<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage users');
    }

    public function index()
    {
        $employees = User::with('roles')->paginate(15);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('employees.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('employees.index')
            ->with('success', 'Employé créé avec succès');
    }

    public function edit(User $employee)
    {
        $roles = Role::all();
        return view('employees.edit', compact('employee', 'roles'));
    }

    public function update(Request $request, User $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $employee->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($request->filled('password')) {
            $employee->update(['password' => Hash::make($validated['password'])]);
        }

        $employee->syncRoles([$validated['role']]);

        return redirect()->route('employees.index')
            ->with('success', 'Employé mis à jour avec succès');
    }

    public function destroy(User $employee)
    {
        if ($employee->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte');
        }

        $employee->delete();
        return redirect()->route('employees.index')
            ->with('success', 'Employé supprimé avec succès');
    }
}