<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Role & Permission matrix oversight is deprecated in favor of streamlined User Management.
     */
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('admin.users.index');
    }
}
