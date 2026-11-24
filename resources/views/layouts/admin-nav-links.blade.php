<!-- Super Admin Dashboard -->
<li>
    <a href="{{ route('admin.dashboard') }}" 
       class="group flex items-center gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-indigo-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
        </svg>
        <span>Console Dashboard</span>
    </a>
</li>

<!-- Users & Role Management -->
<li>
    <a href="{{ route('admin.users.index') }}" 
       class="group flex items-center gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-indigo-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <span class="flex-1">Users & Roles</span>
    </a>
</li>

<!-- Permissions & Module Access Matrix -->
<li>
    <a href="{{ route('admin.permissions.index') }}" 
       class="group flex items-center gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('admin.permissions.*') ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-indigo-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.permissions.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg>
        <span class="flex-1">Permissions Matrix</span>
    </a>
</li>

<div class="pt-3 pb-1">
    <p class="px-2.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Platform Oversight</p>
</div>

<!-- Workshops Oversight -->
<li>
    <a href="{{ route('admin.workshops.index') }}" 
       class="group flex items-center gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('admin.workshops.*') ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-indigo-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.workshops.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z"/>
        </svg>
        <span class="flex-1">Workshops</span>
    </a>
</li>

<!-- Courses & Curriculum -->
<li>
    <a href="{{ route('admin.courses.index') }}" 
       class="group flex items-center gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('admin.courses.*') ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-indigo-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.courses.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <span class="flex-1">Courses & Modules</span>
    </a>
</li>

<!-- Certificate Sign-offs -->
<li>
    <a href="{{ route('admin.certificates.index') }}" 
       class="group flex items-center gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('admin.certificates.*') ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-indigo-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.certificates.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
        </svg>
        <span class="flex-1">Certificate Approvals</span>
    </a>
</li>

<!-- Platform Reports -->
<li>
    <a href="{{ route('admin.reports.index') }}" 
       class="group flex items-center gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-indigo-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <span class="flex-1">Platform Reports</span>
    </a>
</li>
