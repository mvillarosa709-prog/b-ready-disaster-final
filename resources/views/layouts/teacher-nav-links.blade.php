<!-- Dashboard -->
<li>
    <a href="{{ route('teacher.dashboard') }}" 
       class="group flex gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('teacher.dashboard') ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-indigo-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('teacher.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>
</li>

<!-- Discover Workshops -->
<li>
    <a href="{{ route('teacher.workshops.index') }}" 
       class="group flex gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('teacher.workshops.index') || request()->routeIs('teacher.workshops.show') ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-indigo-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('teacher.workshops.index') || request()->routeIs('teacher.workshops.show') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        Discover Workshops
    </a>
</li>

<!-- My Workshops -->
<li>
    <a href="{{ route('teacher.workshops.my') }}" 
       class="group flex gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('teacher.workshops.my') ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-indigo-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('teacher.workshops.my') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        My Workshops
    </a>
</li>

<!-- Workshop Packages -->
<li>
    <a href="{{ route('teacher.packages.index') }}" 
       class="group flex gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('teacher.packages.*') ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-indigo-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('teacher.packages.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        Workshop Packages
    </a>
</li>

<!-- Classroom Implementation -->
<li>
    <a href="{{ route('teacher.implementations.index') }}" 
       class="group flex gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('teacher.implementations.*') ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-indigo-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('teacher.implementations.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Implementations
    </a>
</li>

<!-- Personal Reports -->
<li>
    <a href="{{ route('teacher.reports.index') }}" 
       class="group flex gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('teacher.reports.*') ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-indigo-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('teacher.reports.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Personal Reports
    </a>
</li>
