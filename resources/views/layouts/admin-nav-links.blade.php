<!-- Super Admin Dashboard -->
<li>
    <a href="{{ route('admin.dashboard') }}" 
       class="group flex items-center gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white shadow-xs shadow-emerald-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-emerald-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span>Dashboard</span>
    </a>
</li>

<!-- Users & Role Management -->
<li>
    <a href="{{ route('admin.users.index') }}" 
       class="group flex items-center gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-emerald-600 text-white shadow-xs shadow-emerald-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-emerald-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <span class="flex-1">Users & Roles</span>
    </a>
</li>

<!-- Workshops Oversight -->
<li>
    <a href="{{ route('admin.workshops.index') }}" 
       class="group flex items-center gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('admin.workshops.*') ? 'bg-emerald-600 text-white shadow-xs shadow-emerald-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-emerald-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.workshops.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z"/>
        </svg>
        <span class="flex-1">Workshops</span>
    </a>
</li>

<!-- Courses & Curriculum -->
<li>
    <a href="{{ route('admin.courses.index') }}" 
       class="group flex items-center gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('admin.courses.*') ? 'bg-emerald-600 text-white shadow-xs shadow-emerald-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-emerald-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.courses.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <span class="flex-1">Course Management</span>
    </a>
</li>

<!-- System Reports & Analytics -->
<li>
    <a href="{{ route('admin.reports.index') }}" 
       class="group flex items-center gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-emerald-600 text-white shadow-xs shadow-emerald-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-emerald-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <span class="flex-1">Monitoring & Reports</span>
    </a>
</li>
