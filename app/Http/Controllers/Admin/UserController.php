<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BrevoMailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        protected BrevoMailService $brevoMailService
    ) {}
    /**
     * Display a listing of all system users.
     */
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && in_array($request->role, ['admin', 'trainer', 'teacher'])) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'trainers' => User::where('role', 'trainer')->count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show form for creating a new user with role & permissions.
     */
    public function create(): View
    {
        $allPermissions = User::allAvailablePermissions();
        $defaultTrainerPermissions = User::defaultPermissionsForRole('trainer');
        $defaultTeacherPermissions = User::defaultPermissionsForRole('teacher');

        return view('admin.users.create', compact('allPermissions', 'defaultTrainerPermissions', 'defaultTeacherPermissions'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $allPermKeys = array_keys(User::allAvailablePermissions());

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,trainer,teacher'],
            'is_active' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in($allPermKeys)],
        ]);

        $permissions = $validated['role'] === 'admin'
            ? $allPermKeys
            : ($validated['permissions'] ?? User::defaultPermissionsForRole($validated['role']));

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active', true),
            'permissions' => $permissions,
            'email_verified_at' => now(),
            'must_change_password' => true,
        ]);

        if ($request->boolean('send_gmail', true)) {
            $mailResult = $this->brevoMailService->sendInvitation(
                $user,
                $validated['password'],
                route('login')
            );
        } else {
            $mailResult = [
                'success' => false,
                'message' => 'Skipped email dispatch based on option.',
                'method' => 'none',
            ];
        }

        $redirect = redirect()->route('admin.users.index')
            ->with('new_user_provisioned', [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $validated['password'],
                'role' => $user->role,
                'login_url' => route('login'),
                'mail_sent' => $mailResult['success'],
                'mail_message' => $mailResult['message'],
            ]);

        if ($mailResult['success']) {
            return $redirect->with('success', "Nagawa ang account ni '{$user->name}' bilang {$user->role} at naipadala ang login link at credentials sa Gmail ({$user->email}) gamit ang Brevo!");
        }

        return $redirect->with('warning', "Nagawa ang account ni '{$user->name}', ngunit HINDI pa naipadala sa Gmail: {$mailResult['message']}");
    }

    /**
     * Show form for editing an existing user and their permissions.
     */
    public function edit(User $user): View
    {
        $allPermissions = User::allAvailablePermissions();
        $userEffectivePermissions = $user->getEffectivePermissions();

        return view('admin.users.edit', compact('user', 'allPermissions', 'userEffectivePermissions'));
    }

    /**
     * Update the user and their assigned role/permissions.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $allPermKeys = array_keys(User::allAvailablePermissions());

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'in:admin,trainer,teacher'],
            'is_active' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in($allPermKeys)],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->is_active = $request->boolean('is_active', true);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($validated['role'] === 'admin') {
            $user->permissions = $allPermKeys;
        } else {
            // If permissions were explicitly submitted, use them, otherwise assign role defaults
            $user->permissions = $request->has('permissions')
                ? ($validated['permissions'] ?? [])
                : User::defaultPermissionsForRole($validated['role']);
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', "Account and access permissions for '{$user->name}' updated successfully.");
    }

    /**
     * Toggle active/inactive status of a user.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own administrative account.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $statusLabel = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "User '{$user->name}' has been {$statusLabel}.");
    }

    /**
     * Delete a user account.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own administrative account.');
        }

        if ($user->email === 'adminbready@gmail.com') {
            return back()->with('error', 'The primary Super Admin account cannot be deleted.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$name}' was successfully deleted from the platform.");
    }
}
