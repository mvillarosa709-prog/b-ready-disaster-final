<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    /**
     * Display the Role & Permission matrix oversight.
     */
    public function index(Request $request): View
    {
        $permissions = User::allAvailablePermissions();
        $trainerDefaults = User::defaultPermissionsForRole('trainer');
        $teacherDefaults = User::defaultPermissionsForRole('teacher');
        $adminDefaults = User::defaultPermissionsForRole('admin');

        // Users with explicit custom permissions overrides
        $customizedUsers = User::whereNotNull('permissions')
            ->where('role', '!=', 'admin')
            ->get();

        $usersCountByRole = [
            'admin' => User::where('role', 'admin')->count(),
            'trainer' => User::where('role', 'trainer')->count(),
            'teacher' => User::where('role', 'teacher')->count(),
        ];

        return view('admin.permissions.index', compact(
            'permissions',
            'trainerDefaults',
            'teacherDefaults',
            'adminDefaults',
            'customizedUsers',
            'usersCountByRole'
        ));
    }
}
