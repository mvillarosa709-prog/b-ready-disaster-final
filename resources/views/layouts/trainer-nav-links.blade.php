<!-- Trainer Dashboard -->
<li>
    <a href="{{ route('trainer.dashboard') }}" 
       class="group flex gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('trainer.dashboard') ? 'bg-emerald-600 text-white shadow-xs shadow-emerald-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-emerald-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('trainer.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard Overview
    </a>
</li>

<!-- Courses & Modules -->
<li>
    <a href="{{ route('trainer.courses.index') }}" 
       class="group flex gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('trainer.courses.*') || request()->routeIs('trainer.modules.*') || request()->routeIs('trainer.materials.*') ? 'bg-emerald-600 text-white shadow-xs shadow-emerald-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-emerald-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('trainer.courses.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        Course Management
    </a>
</li>

<!-- Assessments & Question Bank -->
<li>
    <a href="{{ route('trainer.assessments.index') }}" 
       class="group flex gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('trainer.assessments.*') ? 'bg-emerald-600 text-white shadow-xs shadow-emerald-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-emerald-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('trainer.assessments.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        Assessment Bank & Quiz
    </a>
</li>

<!-- Trainer Monitoring & Reports -->
<li>
    <a href="{{ route('trainer.reports') }}" 
       class="group flex gap-x-3 rounded-xl p-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('trainer.reports') ? 'bg-emerald-600 text-white shadow-xs shadow-emerald-600/20' : 'text-slate-700 hover:bg-slate-100 hover:text-emerald-600' }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('trainer.reports') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Monitoring & Reports
    </a>
</li>
