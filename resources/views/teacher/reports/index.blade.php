<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">Personal Reports &bull; Teacher Accreditation</x-slot>
    <x-slot name="header">Teacher Personal Reports &amp; Accreditation Dashboard</x-slot>

    <div class="space-y-8" x-data="{ activeTab: 'all' }">
        
        <!-- Back Button -->
        <div>
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-indigo-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
        </div>

        <!-- Top Hero Banner with Performance Summary (Clean White Theme) -->
        <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-200 p-6 sm:p-8 text-gray-900 shadow-sm mb-8">
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="max-w-2xl space-y-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 border border-indigo-200">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        DRR Educator Personal Portfolio
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-gray-900">
                        {{ auth()->user()->name }}'s Accreditation Record
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                        Comprehensive audit of your DRR training progress, final assessment attempts, digital certifications, and post-training classroom student deployments.
                    </p>
                </div>

                <!-- Aggregate Stats Pillbox -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-50 p-4 rounded-2xl border border-gray-200 shrink-0 text-center">
                    <div class="p-2">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-500">Workshops</span>
                        <span class="text-2xl font-black text-gray-900">{{ $trainingProgress->count() }}</span>
                    </div>
                    <div class="p-2">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-amber-600">Certificates</span>
                        <span class="text-2xl font-black text-amber-600">{{ $certifications->count() }}</span>
                    </div>
                    <div class="p-2">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-sky-600">Students Reached</span>
                        <span class="text-2xl font-black text-sky-600">{{ number_format($totalStudentsReached) }}</span>
                    </div>
                    <div class="p-2">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-emerald-600">Class Average</span>
                        <span class="text-2xl font-black text-emerald-600">{{ $overallClassAverage }}%</span>
                    </div>
                </div>
            </div>

            <!-- Quick Navigation Tabs -->
            <div class="relative z-10 mt-6 pt-5 border-t border-gray-200 flex flex-wrap gap-2 text-xs font-bold">
                <button type="button" @click="activeTab = 'all'"
                        :class="activeTab === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-xl transition-all">
                    All Reports
                </button>
                <button type="button" @click="activeTab = 'progress'"
                        :class="activeTab === 'progress' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-xl transition-all">
                    1. Training Progress ({{ $trainingProgress->count() }})
                </button>
                <button type="button" @click="activeTab = 'assessment'"
                        :class="activeTab === 'assessment' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-xl transition-all">
                    2. Assessment Logs ({{ $assessmentAttempts->count() }})
                </button>
                <button type="button" @click="activeTab = 'certificate'"
                        :class="activeTab === 'certificate' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-xl transition-all">
                    3. Digital Certificates ({{ $certifications->count() }})
                </button>
                <button type="button" @click="activeTab = 'implementation'"
                        :class="activeTab === 'implementation' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-xl transition-all">
                    4. Classroom Implementations ({{ $totalImplementations }})
                </button>
            </div>
        </div>

        <!-- 1. MY TRAINING PROGRESS -->
        <section x-show="activeTab === 'all' || activeTab === 'progress'" class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-indigo-50 text-indigo-700 font-extrabold text-xs">01</span>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">My Training Progress</h3>
                        <p class="text-xs text-slate-500">Workshops joined, sequential modules completed, and completion percentages.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" @click="activeTab = 'all'" x-show="activeTab === 'progress'" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                        &larr; Back to All
                    </button>
                    <a href="{{ route('teacher.workshops.my') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Manage Registrations &rarr;
                    </a>
                </div>
            </div>

            @if ($trainingProgress->isEmpty())
                <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-8 text-center text-xs text-slate-500">
                    You have not registered for any disaster risk reduction workshops yet.
                    <div class="mt-3">
                        <a href="{{ route('teacher.workshops.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 text-white font-bold text-xs shadow-xs hover:bg-indigo-700">
                            Discover Workshops
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach ($trainingProgress as $item)
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between hover:border-indigo-300 transition-all">
                            <div>
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <span class="inline-block text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-100 rounded-md px-2.5 py-0.5">
                                            {{ $item->workshop->course->title ?? 'DRR Foundation' }}
                                        </span>
                                        <h4 class="mt-2 text-base font-extrabold text-slate-900">{{ $item->workshop->title }}</h4>
                                    </div>
                                    @if ($item->is_completed)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 border border-emerald-200 shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Completed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700 border border-amber-200 shrink-0">
                                            In Progress
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-4">
                                    <div class="flex justify-between text-xs font-semibold text-slate-600 mb-1.5">
                                        <span>Curriculum Progress</span>
                                        <span class="font-extrabold text-slate-900">{{ $item->progress_percentage }}% ({{ $item->completed_modules }}/{{ $item->total_modules }} Modules)</span>
                                    </div>
                                    <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-600 rounded-full transition-all duration-500" style="width: {{ $item->progress_percentage }}%"></div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                    <span>Joined: <strong>{{ $item->enrollment->joined_at ? $item->enrollment->joined_at->format('M d, Y') : 'Active' }}</strong></span>
                                    <span>Trainer: <strong>{{ $item->workshop->trainer->name ?? 'DRR Trainer' }}</strong></span>
                                </div>
                            </div>

                            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                                <a href="{{ route('teacher.workshops.show', $item->workshop) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                                    View Syllabus
                                </a>

                                @if ($item->next_module)
                                    <a href="{{ route('teacher.learning.module', [$item->workshop, $item->next_module]) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition-colors">
                                        Resume Module {{ $item->next_module->sequence }} &rarr;
                                    </a>
                                @else
                                    <a href="{{ route('teacher.workshops.show', $item->workshop) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-colors">
                                        Review Course &bull; 100%
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- 2. MY ASSESSMENT -->
        <section x-show="activeTab === 'all' || activeTab === 'assessment'" class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-amber-50 text-amber-700 font-extrabold text-xs">02</span>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">My Assessments</h3>
                        <p class="text-xs text-slate-500">Detailed attempt history, percentage scores, passing threshold, and evaluation results.</p>
                    </div>
                </div>
                <button type="button" @click="activeTab = 'all'" x-show="activeTab === 'assessment'" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                    &larr; Back to All
                </button>
            </div>

            @if ($assessmentAttempts->isEmpty())
                <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-8 text-center text-xs text-slate-500">
                    No assessment attempts recorded yet. Finish all required modules in your workshops to unlock the final examination.
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-600 font-bold uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="p-4">Workshop / Exam</th>
                                    <th class="p-4 text-center">Attempt</th>
                                    <th class="p-4 text-center">Score</th>
                                    <th class="p-4 text-center">Percentage</th>
                                    <th class="p-4 text-center">Required</th>
                                    <th class="p-4 text-center">Result Status</th>
                                    <th class="p-4 text-center">Date Taken</th>
                                    <th class="p-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($assessmentAttempts as $att)
                                    @php
                                        $isPassed = $att->result === 'passed';
                                        $passingScore = $att->assessment->passing_score ?? 70;
                                    @endphp
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="p-4 font-bold text-slate-900">
                                            {{ $att->assessment->workshop->title ?? 'DRR Workshop' }}
                                            <span class="block text-[11px] font-normal text-slate-500 mt-0.5">{{ $att->assessment->title }}</span>
                                        </td>
                                        <td class="p-4 text-center font-bold text-slate-700">#{{ $att->attempt_number }}</td>
                                        <td class="p-4 text-center font-semibold text-slate-800">{{ $att->score }} pts</td>
                                        <td class="p-4 text-center">
                                            <span class="text-sm font-black {{ $isPassed ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ $att->percentage }}%
                                            </span>
                                        </td>
                                        <td class="p-4 text-center text-slate-500 font-medium">{{ $passingScore }}%</td>
                                        <td class="p-4 text-center">
                                            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $isPassed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                                {{ $isPassed ? 'PASSED' : 'FAILED' }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-center text-slate-500">
                                            {{ $att->submitted_at ? $att->submitted_at->format('M d, Y &bull; h:i A') : 'Submitted' }}
                                        </td>
                                        <td class="p-4 text-right">
                                            @if ($att->assessment && $att->assessment->workshop)
                                                <a href="{{ route('teacher.assessments.result', [$att->assessment->workshop, $att]) }}"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-indigo-600 hover:bg-slate-50 shadow-2xs transition-colors">
                                                    View Feedback
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </section>

        <!-- 3. MY CERTIFICATES & DIGITAL BADGES -->
        <section x-show="activeTab === 'all' || activeTab === 'certificate'" class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-amber-50 text-amber-700 font-extrabold text-xs">03</span>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">My Certificates &amp; Digital Badges</h3>
                        <p class="text-xs text-slate-500">Official accreditation credentials, verifiable badge view, and printable certificates.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" @click="activeTab = 'all'" x-show="activeTab === 'certificate'" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                        &larr; Back to All
                    </button>
                    <span class="text-xs font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-full">
                        {{ $certifications->count() }} Issued Credentials
                    </span>
                </div>
            </div>

            @if ($certifications->isEmpty())
                <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-8 text-center text-xs text-slate-500">
                    No certifications earned yet. Score at or above the passing mark on a workshop final exam to earn your official certificate and digital badge.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($certifications as $cert)
                        <div class="bg-white rounded-3xl border border-amber-200/80 p-6 shadow-sm flex flex-col justify-between relative overflow-hidden group hover:shadow-md hover:border-amber-400 transition-all">
                            <div class="absolute -top-12 -right-12 w-36 h-36 bg-amber-400/10 rounded-full blur-2xl pointer-events-none"></div>

                            <div>
                                <div class="flex items-center gap-4">
                                    <div class="h-16 w-16 rounded-2xl bg-gradient-to-tr from-amber-600 via-amber-400 to-yellow-200 p-1 shadow-md border-2 border-amber-500/40 shrink-0 flex items-center justify-center">
                                        <div class="h-full w-full rounded-xl bg-slate-900 text-amber-300 flex flex-col items-center justify-center text-center p-1">
                                            <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-[7px] font-extrabold tracking-widest uppercase mt-0.5 text-amber-200">DRR</span>
                                        </div>
                                    </div>

                                    <div>
                                        <span class="text-[10px] font-black uppercase tracking-widest text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                            {{ $cert->badge_name }}
                                        </span>
                                        <h4 class="mt-1 text-sm font-extrabold text-slate-900 leading-tight">
                                            {{ $cert->workshop->title }}
                                        </h4>
                                    </div>
                                </div>

                                <div class="mt-4 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-1 text-slate-600">
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Certificate No:</span>
                                        <span class="font-mono font-bold text-slate-900">{{ $cert->certificate_number }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Date Issued:</span>
                                        <span class="font-semibold text-slate-700">{{ $cert->certified_at ? $cert->certified_at->format('M d, Y') : now()->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Trainer:</span>
                                        <span class="font-semibold text-slate-700">{{ $cert->workshop->trainer->name ?? 'DRR Specialist' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                                <a href="{{ route('teacher.assessments.certificate', $cert->workshop) }}" target="_blank"
                                   class="flex-1 text-center py-2 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-xs transition-colors">
                                    View / Print &rarr;
                                </a>

                                @if ($cert->workshop->classroomPackage)
                                    <a href="{{ route('teacher.packages.show', $cert->workshop->classroomPackage) }}"
                                       class="py-2 px-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-indigo-700 font-bold text-xs transition-colors shadow-2xs">
                                        Repository
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- 4. MY CLASSROOM IMPLEMENTATION -->
        <section x-show="activeTab === 'all' || activeTab === 'implementation'" class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-sky-50 text-sky-700 font-extrabold text-xs">04</span>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">My Classroom Implementations</h3>
                        <p class="text-xs text-slate-500">Past teaching logs, aggregate student reach, dynamically computed class averages, and written reflections.</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" @click="activeTab = 'all'" x-show="activeTab === 'implementation'" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                        &larr; Back to All
                    </button>
                    <a href="{{ route('teacher.implementations.create') }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-colors">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Log New Session
                    </a>
                </div>
            </div>

            <!-- Aggregate Performance Summary Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Sessions</span>
                    <div class="mt-2 text-3xl font-black text-slate-900">{{ $totalImplementations }}</div>
                    <span class="text-[11px] text-slate-500 mt-1 block">Completed rollouts</span>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Students Reached</span>
                    <div class="mt-2 text-3xl font-black text-indigo-600">{{ number_format($totalStudentsReached) }}</div>
                    <span class="text-[11px] text-slate-500 mt-1 block">Anonymously evaluated</span>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Students Passed / Failed</span>
                    <div class="mt-2 text-2xl font-black text-emerald-600">
                        {{ $totalPassedStudents }} <span class="text-xs font-semibold text-slate-400">pass</span> &bull; 
                        <span class="text-rose-600">{{ $totalFailedStudents }}</span> <span class="text-xs font-semibold text-slate-400">fail</span>
                    </div>
                    <span class="text-[11px] text-slate-500 mt-1 block">70% passing standard</span>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Dynamically Computed Average</span>
                    <div class="mt-2 text-3xl font-black text-sky-600">{{ $overallClassAverage }}%</div>
                    <span class="text-[11px] text-slate-500 mt-1 block">Database AVG aggregation</span>
                </div>
            </div>

            @if ($implementations->isEmpty())
                <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-8 text-center text-xs text-slate-500">
                    No classroom implementations recorded yet. Once you obtain your DRR certification, roll out the workshop repository materials with your learners and log scores here!
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($implementations as $imp)
                        @php
                            $sessionAverage = round((float) $imp->studentResults->avg('percentage'), 1);
                        @endphp
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs space-y-4 hover:border-slate-300 transition-all">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                                <div>
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">
                                        {{ $imp->workshop->title }}
                                    </span>
                                    <h4 class="mt-1 text-base font-extrabold text-slate-900">
                                        {{ $imp->classroomPackage->title }}
                                    </h4>
                                </div>

                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-slate-500">
                                        Conducted: <strong class="text-slate-800">{{ $imp->implementation_date->format('M d, Y') }}</strong>
                                    </span>
                                    <a href="{{ route('teacher.implementations.show', $imp) }}" 
                                       class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-2xs transition-colors">
                                        Detailed Analytics &rarr;
                                    </a>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs">
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Students Reached:</span>
                                    <span class="font-extrabold text-slate-900 text-sm">{{ $imp->students_participated }} Learners</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Passed:</span>
                                    <span class="font-extrabold text-emerald-700 text-sm">{{ $imp->students_passed }} ({{ $imp->students_participated > 0 ? round(($imp->students_passed / $imp->students_participated) * 100) : 0 }}%)</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Failed:</span>
                                    <span class="font-extrabold text-rose-700 text-sm">{{ $imp->students_failed }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Computed Class Average:</span>
                                    <span class="font-extrabold text-indigo-700 text-sm">{{ $sessionAverage }}%</span>
                                </div>
                            </div>

                            @if ($imp->teacher_reflection)
                                <div class="text-xs bg-slate-50/70 p-3.5 rounded-xl border border-slate-200/60">
                                    <span class="font-bold text-slate-700 block mb-1 uppercase tracking-wider text-[10px]">Teacher Written Reflection:</span>
                                    <p class="text-slate-600 leading-relaxed whitespace-pre-line">{{ $imp->teacher_reflection }}</p>
                                </div>
                            @endif

                            @if ($imp->remarks)
                                <div class="text-[11px] text-slate-500 italic">
                                    Notes: {{ $imp->remarks }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-dynamic-component>