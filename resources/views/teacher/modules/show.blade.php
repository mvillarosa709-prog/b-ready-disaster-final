<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">Module {{ $module->sequence }}: {{ $module->title }}</x-slot>

    <!-- Header Banner (Full Width) -->
    <div class="bg-white border-b border-slate-200/80">
        <div class="w-full px-4 py-6 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                    <a href="{{ route('teacher.workshops.show', $workshop) }}" class="hover:text-emerald-600 transition font-medium">
                        {{ $workshop->title }}
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 font-semibold">Module {{ $module->sequence }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Module {{ $module->sequence }}: {{ $module->title }}</h1>
            </div>
            <div>
                <a href="{{ route('teacher.workshops.show', $workshop) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Workshop
                </a>
            </div>
        </div>
    </div>

    <div class="w-full px-4 sm:px-6 lg:px-8 mt-6 space-y-6">
        @if ($quizResult && (int) ($quizResult['module_id'] ?? 0) !== (int) $module->id)
            <div class="rounded-2xl border p-5 {{ $quizResult['passed'] ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-rose-200 bg-rose-50 text-rose-900' }}">
                <h2 class="font-bold">
                    {{ $quizResult['passed'] ? 'Quiz passed' : 'Quiz not passed yet' }}
                    &mdash; {{ number_format($quizResult['percentage'], 2) }}%
                </h2>
                <p class="mt-1 text-sm">
                    Score {{ $quizResult['score'] }} / {{ $quizResult['total_points'] }} points.
                    Passing score: {{ $quizResult['passing_score'] }}%.
                    @if ($quizResult['passed'] && ! empty($quizResult['next_module_title']))
                        {{ $quizResult['next_module_title'] }} is now unlocked.
                    @elseif (! $quizResult['passed'] && $quizResult['attempts_left'] > 0)
                        You have {{ $quizResult['attempts_left'] }} retake(s) remaining.
                    @elseif (! $quizResult['passed'])
                        No attempts remain. Contact your trainer for support.
                    @endif
                </p>
            </div>
        @endif

        <!-- Full-Width Layout -->
        <div class="w-full space-y-6">
            <!-- Top Horizontal Module Sequence Navigator -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="mb-3 pb-3 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Module Sequence</h3>
                        <p class="text-xs text-slate-500">Modules unlock strictly in sequential order.</p>
                    </div>
                    <div class="text-xs font-semibold text-slate-500">
                        Module <span class="text-slate-800 font-bold">{{ $module->sequence }}</span> of <span class="text-slate-800 font-bold">{{ $moduleNav->count() }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($moduleNav as $item)
                        @if ($item->is_unlocked)
                            <a href="{{ route('teacher.learning.module', [$workshop, $item->id]) }}"
                               class="flex items-start gap-3 p-3 rounded-xl transition-all {{ $item->is_current ? 'bg-emerald-50/80 border border-emerald-200 text-emerald-900' : 'hover:bg-slate-50 text-slate-700 border border-slate-100' }}">
                                <div class="h-7 w-7 rounded-lg shrink-0 flex items-center justify-center text-xs font-bold {{ $item->is_completed ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($item->is_current ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-600') }}">
                                    @if ($item->is_completed)
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @else
                                        {{ $item->sequence }}
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1 overflow-hidden">
                                    <div class="text-xs font-bold truncate {{ $item->is_current ? 'text-emerald-950 font-extrabold' : '' }}">
                                        {{ $item->title }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $item->duration }} mins &bull; 
                                        @if ($item->is_completed)
                                            <span class="text-emerald-700 font-semibold">Completed</span>
                                        @elseif ($item->is_current)
                                            <span class="text-emerald-700 font-semibold">Currently Viewing</span>
                                        @else
                                            <span>Unlocked</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @else
                            <!-- Locked Module Item -->
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 text-slate-400 opacity-60 cursor-not-allowed">
                                <div class="h-7 w-7 rounded-lg shrink-0 bg-slate-200 text-slate-500 flex items-center justify-center text-xs font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1 overflow-hidden">
                                    <div class="text-xs font-semibold text-slate-500 truncate">{{ $item->title }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">🔒 Complete Module {{ $item->sequence - 1 }} first</div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Full-Width Active Module Content & Training Materials -->
            <div class="w-full space-y-6"
                 x-data="{
                     materialFilter: 'all',
                     totalMaterials: {{ $module->materials->count() }},
                     completedMaterials: {{ ($currentProgress && $currentProgress->progress >= 75) || $latestQuizAttempt ? json_encode($module->materials->pluck('id')->values()) : '[]' }},
                     moduleProgress: {{ $currentProgress ? (int) $currentProgress->progress : 25 }},
                     get allMaterialsCompleted() {
                         return this.totalMaterials === 0 || this.completedMaterials.length >= this.totalMaterials;
                     },
                     isMaterialCompleted(id) {
                         return this.completedMaterials.includes(id);
                     },
                     markMaterialCompleted(id, interactUrl) {
                         if (!this.completedMaterials.includes(id)) {
                             this.completedMaterials.push(id);
                         }
                         if (this.completedMaterials.length >= this.totalMaterials && this.moduleProgress < 75) {
                             this.moduleProgress = 75;
                         }
                         fetch(interactUrl, {
                             method: 'POST',
                             headers: {
                                 'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                 'Accept': 'application/json',
                                 'X-Requested-With': 'XMLHttpRequest'
                             }
                         }).catch(() => {});
                     }
                 }">
                <!-- Module Header & Description -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-5">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-2">
                            <span class="inline-block text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-md px-2.5 py-1">
                                Sequence {{ $module->sequence }}
                            </span>
                            <span class="text-xs text-slate-500 font-medium">Estimated: {{ $module->estimated_duration ?? '30' }} Minutes</span>
                        </div>

                        @if ($currentProgress && $currentProgress->status === 'completed')
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                Module Completed
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 border border-amber-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                In Progress
                            </span>
                        @endif
                    </div>

                    <div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                            {{ $module->title }}
                        </h2>
                        <div class="mt-4 text-sm text-slate-700 leading-relaxed space-y-3">
                            <p>{{ $module->description }}</p>
                        </div>
                    </div>

                    <!-- Visual Progress Bar for Active Module -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                        <div class="flex items-center justify-between text-xs font-semibold mb-1.5">
                            <span class="text-slate-700">Module Completion Progress:</span>
                            <span class="font-bold text-emerald-700 font-mono" x-text="moduleProgress + '%'">{{ $currentProgress ? $currentProgress->progress : 0 }}%</span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" :style="'width: ' + moduleProgress + '%'" style="width: {{ $currentProgress ? $currentProgress->progress : 0 }}%"></div>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-2">
                            <span class="font-semibold text-slate-700">Interactive Learning Flow:</span> Play the training video and review all documents (including the mid-video &amp; mid-document knowledge checks) to unlock the final Module Quiz below.
                        </p>
                    </div>

                    <!-- Learning Objectives -->
                    @if ($module->learning_objectives)
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Key Learning Objectives
                            </h4>
                            <div class="mt-2 text-xs text-slate-600 leading-relaxed whitespace-pre-line">
                                {{ $module->learning_objectives }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Interactive Training Materials Presentation Section -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Training Materials &amp; Interactive Presentations</h3>
                            <p class="text-xs text-slate-500">Watch the video lessons and read through the documents. Answer the midpoint knowledge check inside each material to unlock the final Module Quiz.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold px-3 py-1 rounded-full border transition"
                                  :class="allMaterialsCompleted ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200'">
                                <span x-text="completedMaterials.length">0</span> / <span x-text="totalMaterials">{{ $module->materials->count() }}</span> Completed
                            </span>
                        </div>
                    </div>

                    @php
                        $videoCount = $module->materials->where('type', 'video')->count();
                        $pptxCount = $module->materials->filter(function($m) {
                            $fp = strtolower($m->file_path ?? '');
                            $ti = strtolower($m->title ?? '');
                            return str_ends_with($fp, '.pptx') || str_ends_with($fp, '.ppt') || str_contains($fp, 'cpr') || str_contains($ti, 'pptx') || str_contains($ti, 'cpr') || str_contains($ti, 'breath');
                        })->count();
                        $docCount = $module->materials->count() - $videoCount - $pptxCount;
                    @endphp

                    <!-- Quick Material Switcher Tabs -->
                    <div class="flex flex-wrap items-center justify-between gap-2 p-2 rounded-2xl bg-slate-100/90 border border-slate-200/90">
                        <div class="flex flex-wrap items-center gap-1.5">
                            <button type="button" @click="materialFilter = 'all'"
                                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
                                    :class="materialFilter === 'all' ? 'bg-white text-slate-900 shadow-xs border border-slate-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/60'">
                                <span>All Materials</span>
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-200 text-slate-700 font-mono">{{ $module->materials->count() }}</span>
                            </button>

                            @if ($videoCount > 0)
                                <button type="button" @click="materialFilter = 'video'"
                                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
                                        :class="materialFilter === 'video' ? 'bg-sky-600 text-white shadow-xs' : 'text-slate-600 hover:text-sky-700 hover:bg-sky-50'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Video Demonstration</span>
                                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="materialFilter === 'video' ? 'bg-sky-500 text-white' : 'bg-slate-200 text-slate-700'">{{ $videoCount }}</span>
                                </button>
                            @endif

                            @if ($pptxCount > 0)
                                <button type="button" @click="materialFilter = 'presentation'"
                                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
                                        :class="materialFilter === 'presentation' ? 'bg-orange-600 text-white shadow-xs' : 'text-slate-600 hover:text-orange-700 hover:bg-orange-50'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                                    <span>PowerPoint Deck (.pptx)</span>
                                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="materialFilter === 'presentation' ? 'bg-orange-500 text-white' : 'bg-orange-100 text-orange-800'">{{ $pptxCount }}</span>
                                </button>
                            @endif

                            @if ($docCount > 0)
                                <button type="button" @click="materialFilter = 'document'"
                                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
                                        :class="materialFilter === 'document' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-rose-700 hover:bg-rose-50'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    <span>Documents &amp; Checklists</span>
                                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="materialFilter === 'document' ? 'bg-rose-500 text-white' : 'bg-slate-200 text-slate-700'">{{ $docCount }}</span>
                                </button>
                            @endif
                        </div>

                        <div class="text-[11px] text-slate-500 font-medium hidden md:block">
                            Jump straight to your materials with the filter tabs above.
                        </div>
                    </div>

                    @if ($module->materials->isEmpty())
                        <div class="p-6 text-center text-xs text-slate-400 border border-dashed border-slate-200 rounded-xl">
                            All necessary content for this module is provided in the lecture overview above.
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach ($module->materials as $mat)
                                @php
                                    $type = strtolower($mat->type ?? 'pdf');
                                    $isPptx = str_ends_with(strtolower($mat->file_path ?? ''), '.pptx') 
                                        || str_ends_with(strtolower($mat->file_path ?? ''), '.ppt') 
                                        || str_contains(strtolower($mat->file_path ?? ''), 'cpr')
                                        || str_contains(strtolower($mat->title ?? ''), 'pptx')
                                        || str_contains(strtolower($mat->title ?? ''), 'cpr')
                                        || str_contains(strtolower($mat->title ?? ''), 'breath')
                                        || str_contains(strtolower($mat->title ?? ''), 'simulation');
                                    $matCategory = $type === 'video' ? 'video' : ($isPptx ? 'presentation' : 'document');
                                    $interactUrl = route('teacher.learning.material.interact', [$workshop, $module, $mat]);
                                    $downloadUrl = route('teacher.learning.material.download', [$workshop, $module, $mat]);
                                @endphp

                                @if ($type === 'video')
                                    <!-- INTERACTIVE VIDEO PLAYER WITH VISUAL ANIMATION, EMBED, & MID-VIDEO QUIZ -->
                                    <div class="rounded-2xl border border-slate-200/90 bg-slate-50/40 overflow-hidden shadow-xs"
                                         x-show="materialFilter === 'all' || materialFilter === 'video'"
                                         x-data="{
                                             playing: false,
                                             viewMode: 'visual',
                                             currentStage: 1,
                                             progress: isMaterialCompleted({{ $mat->id }}) ? 100 : 0,
                                             duration: 12,
                                             currentTime: isMaterialCompleted({{ $mat->id }}) ? 12 : 0,
                                             midQuizTriggered: isMaterialCompleted({{ $mat->id }}),
                                             midQuizOpen: false,
                                             midQuizPassed: isMaterialCompleted({{ $mat->id }}),
                                             selectedAnswer: null,
                                             quizFeedback: '',
                                             timer: null,
                                             setStage(s) {
                                                 this.currentStage = s;
                                                 if (s === 1) { this.currentTime = 0; this.progress = 0; }
                                                 if (s === 2) { this.currentTime = 3.5; this.progress = 30; }
                                                 if (s === 3) { this.currentTime = 7; this.progress = 60; }
                                                 if (s === 4) { this.currentTime = 10.5; this.progress = 85; }
                                             },
                                             togglePlay() {
                                                 if (this.midQuizOpen) return;
                                                 if (this.progress >= 100) {
                                                     this.progress = 0;
                                                     this.currentTime = 0;
                                                 }
                                                 this.playing = !this.playing;
                                                 if (this.playing) {
                                                     this.startTimer();
                                                 } else {
                                                     clearInterval(this.timer);
                                                 }
                                             },
                                             startTimer() {
                                                 clearInterval(this.timer);
                                                 this.timer = setInterval(() => {
                                                     if (!this.playing) return;
                                                     this.currentTime = Math.min(this.duration, +(this.currentTime + 0.2).toFixed(1));
                                                     this.progress = Math.round((this.currentTime / this.duration) * 100);

                                                     if (this.progress < 25) this.currentStage = 1;
                                                     else if (this.progress < 50) this.currentStage = 2;
                                                     else if (this.progress < 75) this.currentStage = 3;
                                                     else this.currentStage = 4;

                                                     // Pause in the middle of the video (50%) for Mid-Video Quiz!
                                                     if (this.progress >= 50 && !this.midQuizTriggered) {
                                                         this.playing = false;
                                                         clearInterval(this.timer);
                                                         this.currentTime = this.duration / 2;
                                                         this.progress = 50;
                                                         this.midQuizTriggered = true;
                                                         this.midQuizOpen = true;
                                                         return;
                                                     }

                                                     // Video finished (100%)
                                                     if (this.progress >= 100) {
                                                         this.playing = false;
                                                         clearInterval(this.timer);
                                                         this.progress = 100;
                                                         this.currentTime = this.duration;
                                                         this.markMaterialCompleted({{ $mat->id }}, '{{ $interactUrl }}');
                                                     }
                                                 }, 200);
                                             },
                                             submitMidQuiz() {
                                                 if (this.selectedAnswer === 'correct') {
                                                     this.midQuizPassed = true;
                                                     this.midQuizOpen = false;
                                                     this.quizFeedback = '';
                                                     this.playing = true;
                                                     this.startTimer();
                                                 } else {
                                                     this.quizFeedback = 'Not quite right. Review the safety principle and select the correct protocol to continue the video.';
                                                 }
                                             },
                                             formatTime(sec) {
                                                 const s = Math.floor(sec);
                                                 return '0:' + (s < 10 ? '0' + s : s);
                                             }
                                         }">
                                        <!-- Material Header -->
                                        <div class="p-4 sm:px-6 bg-white border-b border-slate-200/80 flex flex-wrap items-center justify-between gap-3">
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 rounded-xl bg-sky-50 text-sky-600 border border-sky-200 flex items-center justify-center shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-700 bg-sky-50 border border-sky-200 px-2 py-0.5 rounded">
                                                            Video Presentation
                                                        </span>
                                                        <span class="text-[10px] font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded">
                                                            ⚡ Mid-Video Quiz at 50%
                                                        </span>
                                                    </div>
                                                    <h4 class="text-sm font-bold text-slate-900 mt-1">{{ $mat->title }}</h4>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2.5">
                                                <template x-if="isMaterialCompleted({{ $mat->id }})">
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                        ✓ Video &amp; Quiz Completed
                                                    </span>
                                                </template>

                                                <!-- Mode Switcher Buttons -->
                                                <div class="flex items-center rounded-xl bg-slate-100 p-0.5 border border-slate-200 text-xs">
                                                    <button type="button" @click="viewMode = 'visual'"
                                                            class="px-2.5 py-1 rounded-lg font-bold transition cursor-pointer"
                                                            :class="viewMode === 'visual' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                                                        🎬 Visual Drill Walk-Through
                                                    </button>
                                                    <button type="button" @click="viewMode = 'video'"
                                                            class="px-2.5 py-1 rounded-lg font-bold transition cursor-pointer"
                                                            :class="viewMode === 'video' ? 'bg-white text-sky-700 shadow-xs' : 'text-slate-600 hover:text-sky-700'">
                                                        📺 DepEd Video Stream
                                                    </button>
                                                </div>

                                                <a href="{{ $downloadUrl }}" {{ $mat->external_url ? 'target="_blank"' : '' }}
                                                   class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 hover:text-emerald-700 bg-slate-100 hover:bg-emerald-50 px-3 py-1.5 rounded-xl border border-slate-200 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                    </svg>
                                                    <span>Download Video</span>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Interactive Video Stage -->
                                        <div class="relative bg-slate-950 text-white min-h-[340px] sm:min-h-[380px] flex flex-col justify-between p-6 select-none">
                                            <!-- Top Video Overlay Info -->
                                            <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-300 z-10">
                                                <div class="flex items-center gap-2">
                                                    <span class="inline-flex items-center gap-1.5 bg-slate-800/90 border border-slate-700 px-3 py-1 rounded-full font-semibold">
                                                        <span class="h-2 w-2 rounded-full" :class="playing ? 'bg-red-500 animate-ping' : 'bg-emerald-400'"></span>
                                                        <span x-text="'Stage ' + currentStage + ' of 4: ' + (currentStage === 1 ? 'Earthquake Warning & Alarm' : (currentStage === 2 ? 'Duck, Cover, and Hold' : (currentStage === 3 ? 'Classroom Evacuation' : 'Safe Assembly Area Triage')))"></span>
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <span class="font-mono bg-slate-800/90 px-2.5 py-1 rounded-md border border-slate-700 text-emerald-400 font-bold"
                                                          x-text="formatTime(currentTime) + ' / ' + formatTime(duration)">0:00 / 0:12</span>
                                                </div>
                                            </div>

                                            <!-- CENTER VIEW: EITHER VISUAL MULTI-SCENE ANIMATION OR YOUTUBE EMBED -->
                                            <div class="my-4 w-full max-w-2xl mx-auto z-10">
                                                <!-- View 1: Dynamic Visual Simulation Scenes -->
                                                <div x-show="viewMode === 'visual'" class="space-y-4">
                                                    <!-- Scene 1: Seismic Warning (0-25%) -->
                                                    <div x-show="currentStage === 1" class="p-6 rounded-2xl bg-gradient-to-br from-red-950 via-slate-900 to-amber-950 border border-red-500/30 text-center space-y-3 shadow-xl">
                                                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-red-500/20 text-red-400 border border-red-500/40 mx-auto animate-pulse">
                                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                            </svg>
                                                        </div>
                                                        <span class="inline-block text-[11px] font-bold uppercase tracking-widest text-red-400 bg-red-950/80 px-2.5 py-1 rounded-full border border-red-700/50">
                                                            🚨 Stage 1: Seismic Alarm Triggered (Intensity VII)
                                                        </span>
                                                        <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                                                            DUCK, COVER, AND HOLD!
                                                        </h3>
                                                        <p class="text-xs sm:text-sm text-slate-300 max-w-lg mx-auto leading-relaxed">
                                                            Shaking has commenced! Issue loud verbal warning: Drop to knees, seek immediate shelter beneath sturdy wooden desks, and firmly grasp desk legs. Stay clear of glass windows and overhead fixtures.
                                                        </p>
                                                    </div>

                                                    <!-- Scene 2: Structural Inspection & Door Unlatching (25-50%) -->
                                                    <div x-show="currentStage === 2" style="display: none;" class="p-6 rounded-2xl bg-gradient-to-br from-amber-950 via-slate-900 to-orange-950 border border-amber-500/30 text-center space-y-3 shadow-xl">
                                                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-500/20 text-amber-300 border border-amber-500/40 mx-auto">
                                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                                                            </svg>
                                                        </div>
                                                        <span class="inline-block text-[11px] font-bold uppercase tracking-widest text-amber-300 bg-amber-950/80 px-2.5 py-1 rounded-full border border-amber-700/50">
                                                            🔍 Stage 2: Exit Path Verification &amp; Door Unlatching
                                                        </span>
                                                        <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                                                            Secure Clear Egress Before Evacuating
                                                        </h3>
                                                        <p class="text-xs sm:text-sm text-slate-300 max-w-lg mx-auto leading-relaxed">
                                                            Teachers must unlatch and prop classroom doors open to prevent frame jamming from structural shifting. Grab the Classroom Emergency Go-Bag, First Aid Kit, and Class Attendance Masterlist.
                                                        </p>
                                                    </div>

                                                    <!-- Scene 3: Orderly Evacuation & Buddy Accounting (50-75%) -->
                                                    <div x-show="currentStage === 3" style="display: none;" class="p-6 rounded-2xl bg-gradient-to-br from-sky-950 via-slate-900 to-indigo-950 border border-sky-500/30 text-center space-y-3 shadow-xl">
                                                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-sky-500/20 text-sky-300 border border-sky-500/40 mx-auto">
                                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                            </svg>
                                                        </div>
                                                        <span class="inline-block text-[11px] font-bold uppercase tracking-widest text-sky-300 bg-sky-950/80 px-2.5 py-1 rounded-full border border-sky-700/50">
                                                            🚶‍♂️ Stage 3: Orderly Marching &amp; 4-NOs Rule
                                                        </span>
                                                        <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                                                            No Running &bull; No Pushing &bull; No Talking &bull; No Turning Back
                                                        </h3>
                                                        <p class="text-xs sm:text-sm text-slate-300 max-w-lg mx-auto leading-relaxed">
                                                            Students cover their heads with books or backpacks while walking in 2-by-2 buddy pairs. Follow the designated green primary evacuation route down stairwells. Never use elevators.
                                                        </p>
                                                    </div>

                                                    <!-- Scene 4: Safe Assembly Zone Staging & Headcount (75-100%) -->
                                                    <div x-show="currentStage === 4" style="display: none;" class="p-6 rounded-2xl bg-gradient-to-br from-emerald-950 via-slate-900 to-teal-950 border border-emerald-500/30 text-center space-y-3 shadow-xl">
                                                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 mx-auto">
                                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                        </div>
                                                        <span class="inline-block text-[11px] font-bold uppercase tracking-widest text-emerald-300 bg-emerald-950/80 px-2.5 py-1 rounded-full border border-emerald-700/50">
                                                            🚩 Stage 4: Open Field Assembly &amp; Headcount Report
                                                        </span>
                                                        <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                                                            100% Learner Accountability Verified
                                                        </h3>
                                                        <p class="text-xs sm:text-sm text-slate-300 max-w-lg mx-auto leading-relaxed">
                                                            All classes line up inside the assigned school open quadrant outside falling hazard radius. Conduct roll call and immediately report status to the School Incident Command Post.
                                                        </p>
                                                    </div>

                                                    <div class="flex flex-wrap items-center justify-center gap-2 pt-1">
                                                        <button type="button" @click="setStage(1)"
                                                                class="px-2.5 py-1 rounded-lg text-xs font-bold transition cursor-pointer"
                                                                :class="currentStage === 1 ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'">
                                                            1. Alarm &amp; Drop
                                                        </button>
                                                        <button type="button" @click="setStage(2)"
                                                                class="px-2.5 py-1 rounded-lg text-xs font-bold transition cursor-pointer"
                                                                :class="currentStage === 2 ? 'bg-amber-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'">
                                                            2. Door Check
                                                        </button>
                                                        <button type="button" @click="setStage(3)"
                                                                class="px-2.5 py-1 rounded-lg text-xs font-bold transition cursor-pointer"
                                                                :class="currentStage === 3 ? 'bg-sky-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'">
                                                            3. Evacuate
                                                        </button>
                                                        <button type="button" @click="setStage(4)"
                                                                class="px-2.5 py-1 rounded-lg text-xs font-bold transition cursor-pointer"
                                                                :class="currentStage === 4 ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'">
                                                            4. Assembly &amp; Tally
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- View 2: Official DepEd NDRRMC Video Stream (YouTube Embed) -->
                                                <div x-show="viewMode === 'video'" style="display: none;" class="space-y-3">
                                                    <div class="relative w-full aspect-video rounded-2xl overflow-hidden bg-black shadow-2xl border border-slate-700">
                                                        <iframe class="w-full h-full"
                                                                src="https://www.youtube-nocookie.com/embed/6YkK8xMh96Q?rel=0&autoplay=0"
                                                                title="DepEd School Earthquake & Disaster Evacuation Drill Video"
                                                                frameborder="0"
                                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                                allowfullscreen>
                                                        </iframe>
                                                    </div>
                                                    <p class="text-[11px] text-center text-slate-400">
                                                        Official Disaster Risk Reduction and Management Drill Demonstration (DepEd / NDRRMC Guidelines).
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Bottom Video Controls & Progress Bar with 50% Quiz Marker -->
                                            <div class="space-y-2.5 z-10">
                                                <div class="relative w-full h-3 bg-slate-800 rounded-full overflow-visible border border-slate-700">
                                                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-150" :style="'width: ' + progress + '%'"></div>
                                                    <!-- 50% Mid-Video Quiz Checkpoint Marker -->
                                                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-4 h-4 rounded-full border-2 border-slate-900 flex items-center justify-center shadow"
                                                         :class="midQuizPassed ? 'bg-emerald-400' : 'bg-amber-400'"
                                                         title="Mid-Video Quiz at 50%">
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-between text-xs text-slate-300">
                                                    <div class="flex items-center gap-3">
                                                        <button type="button" @click="togglePlay()" class="inline-flex items-center gap-1.5 font-bold text-white hover:text-emerald-400 transition cursor-pointer">
                                                            <span x-text="playing ? '⏸ Pause' : (progress >= 100 ? '↻ Replay Simulation' : '▶ Play Simulation')"></span>
                                                        </button>
                                                        <button type="button" @click="markMaterialCompleted({{ $mat->id }}, '{{ $interactUrl }}'); progress = 100; currentTime = duration;"
                                                                class="text-[11px] text-slate-400 hover:text-white underline cursor-pointer">
                                                            Mark Complete
                                                        </button>
                                                    </div>

                                                    <span class="text-[11px] text-amber-300 font-semibold">
                                                        <span x-show="!midQuizPassed">⚡ Pauses at 50% for Mid-Video Knowledge Check</span>
                                                        <span x-show="midQuizPassed" class="text-emerald-400">✓ Mid-Video Knowledge Check Passed</span>
                                                    </span>

                                                    <span class="font-mono text-[11px]" x-text="progress + '%'">0%</span>
                                                </div>
                                            </div>

                                            <!-- MID-VIDEO QUIZ OVERLAY (Pops up at 50% of video!) -->
                                            <div x-show="midQuizOpen"
                                                 style="display: none;"
                                                 class="absolute inset-0 bg-slate-950/90 backdrop-blur-xs p-5 sm:p-6 flex items-center justify-center z-20">
                                                <div class="bg-white text-slate-900 rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-amber-200 space-y-4">
                                                    <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-3">
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-amber-100 text-amber-900">
                                                            ⚡ Mid-Video Knowledge Check (50%)
                                                        </span>
                                                        <button type="button" @click="midQuizPassed = true; midQuizOpen = false; playing = true; startTimer();"
                                                                class="text-xs text-slate-400 hover:text-slate-700 font-bold px-2 py-1 rounded hover:bg-slate-100 cursor-pointer" title="Close Checkpoint">
                                                            ✕ Close
                                                        </button>
                                                    </div>

                                                    <p class="text-sm font-bold text-slate-900">
                                                        Based on the first half of "{{ $mat->title }}", what is the teacher's primary priority during this protocol?
                                                    </p>

                                                    <div class="space-y-2 text-xs">
                                                        <label class="flex items-start gap-2.5 p-3 rounded-xl border cursor-pointer transition"
                                                               :class="selectedAnswer === 'correct' ? 'border-emerald-500 bg-emerald-50/70 font-semibold' : 'border-slate-200 hover:bg-slate-50'">
                                                            <input type="radio" name="mid_quiz_{{ $mat->id }}" value="correct" x-model="selectedAnswer" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                                                            <span>Follow the standardized safety checklist, verify clear evacuation paths, and account for all learners.</span>
                                                        </label>
                                                        <label class="flex items-start gap-2.5 p-3 rounded-xl border cursor-pointer transition"
                                                               :class="selectedAnswer === 'wrong1' ? 'border-rose-300 bg-rose-50/50' : 'border-slate-200 hover:bg-slate-50'">
                                                            <input type="radio" name="mid_quiz_{{ $mat->id }}" value="wrong1" x-model="selectedAnswer" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                                                            <span>Skip the preliminary hazard check and allow students to exit without supervision.</span>
                                                        </label>
                                                        <label class="flex items-start gap-2.5 p-3 rounded-xl border cursor-pointer transition"
                                                               :class="selectedAnswer === 'wrong2' ? 'border-rose-300 bg-rose-50/50' : 'border-slate-200 hover:bg-slate-50'">
                                                            <input type="radio" name="mid_quiz_{{ $mat->id }}" value="wrong2" x-model="selectedAnswer" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                                                            <span>Delay safety preparations until after an incident has already occurred.</span>
                                                        </label>
                                                    </div>

                                                    <p x-show="quizFeedback" x-text="quizFeedback" class="text-xs font-semibold text-rose-600"></p>

                                                    <div class="flex items-center justify-between pt-1">
                                                        <button type="button" @click="midQuizPassed = true; midQuizOpen = false; playing = true; startTimer();"
                                                                class="text-xs text-slate-500 hover:text-slate-800 underline cursor-pointer">
                                                            Skip Checkpoint &rarr;
                                                        </button>
                                                        <button type="button" @click="submitMidQuiz()"
                                                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition cursor-pointer">
                                                            <span>Submit Answer &amp; Continue Video</span>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- INTERACTIVE DOCUMENT / PPTX PRESENTATION VIEWER -->
                                    @php
                                        $isPptx = str_ends_with(strtolower($mat->file_path ?? ''), '.pptx') 
                                            || str_ends_with(strtolower($mat->file_path ?? ''), '.ppt') 
                                            || str_contains(strtolower($mat->file_path ?? ''), 'cpr')
                                            || str_contains(strtolower($mat->title ?? ''), 'pptx')
                                            || str_contains(strtolower($mat->title ?? ''), 'cpr')
                                            || str_contains(strtolower($mat->title ?? ''), 'breath')
                                            || str_contains(strtolower($mat->title ?? ''), 'simulation');
                                    @endphp

                                    <div class="rounded-2xl border border-slate-200/90 bg-white overflow-hidden shadow-xs"
                                         x-show="materialFilter === 'all' || (materialFilter === 'presentation' && {{ $isPptx ? 'true' : 'false' }}) || (materialFilter === 'document' && {{ ! $isPptx ? 'true' : 'false' }})"
                                         x-data="{
                                             page: isMaterialCompleted({{ $mat->id }}) ? 8 : 1,
                                             totalPages: 8,
                                             docQuizPassed: isMaterialCompleted({{ $mat->id }}),
                                             docAnswer: isMaterialCompleted({{ $mat->id }}) ? 'correct' : null,
                                             docFeedback: '',
                                             setSlide(num) {
                                                 this.page = num;
                                             },
                                             nextSlide() {
                                                 if (this.page < this.totalPages) {
                                                     this.page++;
                                                 }
                                                 if (this.page === this.totalPages) {
                                                     this.markMaterialCompleted({{ $mat->id }}, '{{ $interactUrl }}');
                                                 }
                                             },
                                             prevSlide() {
                                                 if (this.page > 1) {
                                                     this.page--;
                                                 }
                                             },
                                             submitSlideQuiz() {
                                                 if (this.docAnswer === 'correct') {
                                                     this.docQuizPassed = true;
                                                     this.docFeedback = '';
                                                     this.markMaterialCompleted({{ $mat->id }}, '{{ $interactUrl }}');
                                                 } else {
                                                     this.docFeedback = 'Please select the correct compression rate (100–120 bpm) and depth (2–2.4 in) to pass the checkpoint.';
                                                 }
                                             }
                                         }">
                                        @if ($isPptx)
                                            <!-- Prominent PowerPoint Deck Banner -->
                                            <div class="px-5 py-3.5 bg-gradient-to-r from-orange-600 via-amber-600 to-rose-600 text-white flex flex-wrap items-center justify-between gap-3 shadow-sm">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="p-1.5 rounded-xl bg-white/20 backdrop-blur-xs">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                                                    </span>
                                                    <div>
                                                        <span class="text-xs font-black uppercase tracking-wider block text-orange-200">⭐ Official Presentation Deck (.pptx)</span>
                                                        <span class="text-sm sm:text-base font-extrabold text-white">"Save your Breath… Save a Life" (30-Slide CPR Deck)</span>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold bg-white/20 px-3 py-1 rounded-full text-white">30 Official Slides</span>
                                                    <a href="{{ $downloadUrl }}"
                                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white text-orange-800 text-xs font-bold hover:bg-orange-50 shadow-sm transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        <span>Download PPTX</span>
                                                    </a>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Document Header -->
                                        <div class="p-4 sm:px-6 bg-slate-50/70 border-b border-slate-200/80 flex flex-wrap items-center justify-between gap-3">
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 rounded-xl flex items-center justify-center shrink-0 {{ $isPptx ? 'bg-orange-50 text-orange-600 border border-orange-200' : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                                                    @if ($isPptx)
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        @if ($isPptx)
                                                            <span class="text-[10px] font-bold uppercase tracking-wider text-orange-700 bg-orange-100 border border-orange-300 px-2 py-0.5 rounded">
                                                                PowerPoint Slide Deck (.pptx)
                                                            </span>
                                                            <span class="text-[10px] font-semibold text-slate-500 bg-white border border-slate-200 px-2 py-0.5 rounded">
                                                                30 Official Presentation Slides
                                                            </span>
                                                        @else
                                                            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700 bg-rose-100 border border-rose-300 px-2 py-0.5 rounded">
                                                                Document Guide (PDF)
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <h4 class="text-sm font-bold text-slate-900 mt-1">{{ $mat->title }}</h4>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2.5">
                                                <template x-if="isMaterialCompleted({{ $mat->id }})">
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                        ✓ Presentation Reviewed
                                                    </span>
                                                </template>
                                                <a href="{{ $downloadUrl }}" {{ $mat->external_url ? 'target="_blank"' : '' }}
                                                   class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 hover:text-emerald-700 bg-white hover:bg-emerald-50 px-3 py-1.5 rounded-xl border border-slate-200 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                    </svg>
                                                    <span>Download {{ $isPptx ? 'PPTX' : 'File' }}</span>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Slide Navigation Pill Bar -->
                                        <div class="px-4 sm:px-6 py-2.5 bg-slate-100/70 border-b border-slate-200/70 flex items-center justify-between gap-2 overflow-x-auto">
                                            <div class="flex items-center gap-1.5 shrink-0 text-xs font-bold">
                                                <span class="text-slate-500 mr-1">Slides:</span>
                                                <template x-for="s in [1, 2, 3, 4, 5, 6, 7, 8]" :key="s">
                                                    <button type="button" @click="setSlide(s)"
                                                            class="h-7 w-7 rounded-lg text-xs font-bold transition flex items-center justify-center cursor-pointer"
                                                            :class="page === s ? 'bg-orange-600 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-200 border border-slate-200'"
                                                            x-text="s">
                                                    </button>
                                                </template>
                                            </div>
                                            <div class="text-xs text-slate-500 font-medium shrink-0">
                                                <span class="font-bold text-slate-800" x-text="'Slide ' + page + ' of ' + totalPages"></span>
                                            </div>
                                        </div>

                                        <!-- Interactive Slide Body with Real PPT Content -->
                                        <div class="p-6 space-y-5">
                                            <!-- SLIDE 1: Title & Hero -->
                                            <div x-show="page === 1" class="space-y-4">
                                                <div class="p-6 rounded-2xl bg-gradient-to-br from-orange-500 via-amber-600 to-rose-600 text-white shadow-md">
                                                    <span class="text-xs font-bold uppercase tracking-widest text-orange-200 block">Emergency DRR &amp; Basic Life Support Training</span>
                                                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight mt-1">Save your Breath… Save a Life</h2>
                                                    <p class="text-sm sm:text-base text-orange-100 mt-2 max-w-2xl leading-relaxed">
                                                        Cardiopulmonary Resuscitation (Chest-Compression-Only CPR) for Teachers, School Responders, and Disaster Focal Persons.
                                                    </p>
                                                    <div class="mt-4 flex flex-wrap items-center gap-3 text-xs font-semibold">
                                                        <span class="bg-white/20 backdrop-blur-xs px-3 py-1 rounded-full">✓ 100–120 Compressions/Min</span>
                                                        <span class="bg-white/20 backdrop-blur-xs px-3 py-1 rounded-full">✓ 2–2.4 Inches Depth</span>
                                                        <span class="bg-white/20 backdrop-blur-xs px-3 py-1 rounded-full">✓ No Mouth-to-Mouth Required</span>
                                                    </div>
                                                </div>
                                                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-600 space-y-1">
                                                    <p class="font-bold text-slate-800">Teacher Overview:</p>
                                                    <p>This master slide presentation equips educators with hands-on Basic Life Support principles. Click "Next Slide" below or use the slide tabs to review the mandatory legal guidelines, emergency principles, and step-by-step chest compression technique.</p>
                                                </div>
                                            </div>

                                            <!-- SLIDE 2: Legal Basis & School Policy -->
                                            <div x-show="page === 2" style="display: none;" class="space-y-4">
                                                <div class="border-b border-slate-200 pb-2">
                                                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600">Slide 2: Regulatory Framework</span>
                                                    <h3 class="text-lg font-bold text-slate-900 mt-0.5">Legal Basis &amp; School Health Mandates</h3>
                                                </div>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 space-y-1.5">
                                                        <span class="font-bold text-emerald-800 uppercase tracking-wide block">Republic Act 10871</span>
                                                        <p class="font-bold text-slate-900 text-sm">Basic Life Support Training in Schools Act</p>
                                                        <p class="text-slate-600 leading-relaxed">Mandates all public and private basic education schools nationwide to provide age-appropriate Basic Life Support and CPR training to students and teachers.</p>
                                                    </div>
                                                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 space-y-1.5">
                                                        <span class="font-bold text-sky-800 uppercase tracking-wide block">Administrative Order (AO) 155 s. 2004</span>
                                                        <p class="font-bold text-slate-900 text-sm">Mandatory Basic Life Support Policy</p>
                                                        <p class="text-slate-600 leading-relaxed">BLS training is required for all health and disaster response personnel, institutionalizing community-wide life-saving readiness.</p>
                                                    </div>
                                                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 space-y-1.5 md:col-span-2">
                                                        <span class="font-bold text-purple-800 uppercase tracking-wide block">DOH Health Emergency Management Bureau (HEMB) Goal</span>
                                                        <p class="text-slate-800 font-semibold text-sm">"At least one member of each household shall be trained in Basic Life Support (BLS)."</p>
                                                        <p class="text-slate-500">Department Memorandum 2021-0134: Updated Consensus Guidelines on BLS based on the 2020 Guidelines of CPR.</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- SLIDE 3: Sudden Cardiac Arrest Statistics -->
                                            <div x-show="page === 3" style="display: none;" class="space-y-4">
                                                <div class="border-b border-slate-200 pb-2">
                                                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600">Slide 3: Epidemiology</span>
                                                    <h3 class="text-lg font-bold text-slate-900 mt-0.5">Sudden Cardiac Arrest (Out-of-Hospital Crisis)</h3>
                                                </div>
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                    <div class="p-4 rounded-xl border border-rose-200 bg-rose-50/60 text-center">
                                                        <span class="text-2xl font-extrabold text-rose-700">&gt;350,000</span>
                                                        <p class="text-xs text-rose-900 font-medium mt-1">Cardiac arrests outside hospital each year</p>
                                                    </div>
                                                    <div class="p-4 rounded-xl border border-amber-200 bg-amber-50/60 text-center">
                                                        <span class="text-2xl font-extrabold text-amber-700">70%</span>
                                                        <p class="text-xs text-amber-900 font-medium mt-1">Occur in a home or residence</p>
                                                    </div>
                                                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 text-center">
                                                        <span class="text-2xl font-extrabold text-slate-800">Nearly 90%</span>
                                                        <p class="text-xs text-slate-600 font-medium mt-1">Will not survive without immediate bystander CPR</p>
                                                    </div>
                                                </div>
                                                <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/50 text-xs text-emerald-900 space-y-1">
                                                    <p class="font-bold">Bystander Action Saves Lives:</p>
                                                    <p>It takes <strong>4–8 minutes</strong> for emergency responders to arrive. Brain damage begins within 4 minutes without circulation. Immediate bystander chest compressions dramatically increase survival and neurological recovery!</p>
                                                </div>
                                            </div>

                                            <!-- SLIDE 4: Heart Attack vs Cardiac Arrest -->
                                            <div x-show="page === 4" style="display: none;" class="space-y-4">
                                                <div class="border-b border-slate-200 pb-2">
                                                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600">Slide 4: Critical Differentiation</span>
                                                    <h3 class="text-lg font-bold text-slate-900 mt-0.5">Heart Attack vs. Cardiac Arrest</h3>
                                                </div>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                                    <div class="p-4 rounded-xl border border-blue-200 bg-blue-50/50 space-y-2">
                                                        <span class="inline-block px-2.5 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800">Heart Attack (Circulation Problem)</span>
                                                        <ul class="space-y-1.5 list-disc list-inside text-slate-700">
                                                            <li>Blockage / clot in a coronary artery preventing blood flow to heart muscle.</li>
                                                            <li>Person is <strong>usually conscious</strong> and able to talk.</li>
                                                            <li>Symptoms: Crushing chest pressure, left arm / jaw pain, shortness of breath.</li>
                                                            <li>Action: Keep patient calm, position comfortably, call emergency services immediately.</li>
                                                        </ul>
                                                    </div>
                                                    <div class="p-4 rounded-xl border border-rose-200 bg-rose-50/50 space-y-2">
                                                        <span class="inline-block px-2.5 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-800">Cardiac Arrest (Electrical Problem)</span>
                                                        <ul class="space-y-1.5 list-disc list-inside text-slate-700">
                                                            <li>Heart electrical system malfunctions and abruptly stops pumping blood.</li>
                                                            <li>Person is <strong>unconscious and unresponsive</strong>.</li>
                                                            <li>Person is <strong>not breathing or only gasping (agonal breathing)</strong>.</li>
                                                            <li>Action: <strong>Call emergency hotline &amp; start CHEST COMPRESSIONS IMMEDIATELY!</strong></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- SLIDE 5: Stroke Assessment (B.E. F.A.S.T.) -->
                                            <div x-show="page === 5" style="display: none;" class="space-y-4">
                                                <div class="border-b border-slate-200 pb-2">
                                                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600">Slide 5: Neurological Assessment</span>
                                                    <h3 class="text-lg font-bold text-slate-900 mt-0.5">Cerebrovascular Disease: B.E. F.A.S.T. for Stroke</h3>
                                                </div>
                                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                                                    <div class="p-3 rounded-xl border border-slate-200 bg-white shadow-2xs space-y-1">
                                                        <span class="h-6 w-6 rounded-md bg-orange-100 text-orange-800 font-bold flex items-center justify-center text-xs">B</span>
                                                        <p class="font-bold text-slate-900">Balance</p>
                                                        <p class="text-slate-500">Sudden dizziness, headache, loss of coordination?</p>
                                                    </div>
                                                    <div class="p-3 rounded-xl border border-slate-200 bg-white shadow-2xs space-y-1">
                                                        <span class="h-6 w-6 rounded-md bg-orange-100 text-orange-800 font-bold flex items-center justify-center text-xs">E</span>
                                                        <p class="font-bold text-slate-900">Eyes</p>
                                                        <p class="text-slate-500">Blurry vision, double vision, or uneven pupils?</p>
                                                    </div>
                                                    <div class="p-3 rounded-xl border border-slate-200 bg-white shadow-2xs space-y-1">
                                                        <span class="h-6 w-6 rounded-md bg-orange-100 text-orange-800 font-bold flex items-center justify-center text-xs">F</span>
                                                        <p class="font-bold text-slate-900">Face</p>
                                                        <p class="text-slate-500">Does one side of the face droop? Ask to smile.</p>
                                                    </div>
                                                    <div class="p-3 rounded-xl border border-slate-200 bg-white shadow-2xs space-y-1">
                                                        <span class="h-6 w-6 rounded-md bg-orange-100 text-orange-800 font-bold flex items-center justify-center text-xs">A</span>
                                                        <p class="font-bold text-slate-900">Arms</p>
                                                        <p class="text-slate-500">Can they raise both arms? Is one arm weak/numb?</p>
                                                    </div>
                                                    <div class="p-3 rounded-xl border border-slate-200 bg-white shadow-2xs space-y-1">
                                                        <span class="h-6 w-6 rounded-md bg-orange-100 text-orange-800 font-bold flex items-center justify-center text-xs">S</span>
                                                        <p class="font-bold text-slate-900">Speech</p>
                                                        <p class="text-slate-500">Slurred speech? Ask to repeat a simple sentence.</p>
                                                    </div>
                                                    <div class="p-3 rounded-xl border border-rose-200 bg-rose-50 shadow-2xs space-y-1">
                                                        <span class="h-6 w-6 rounded-md bg-rose-200 text-rose-800 font-bold flex items-center justify-center text-xs">T</span>
                                                        <p class="font-bold text-rose-900">Time to Call!</p>
                                                        <p class="text-rose-700">Call DRR / 911 immediately. Get to hospital now!</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- SLIDE 6: Emergency Action Principles & Call First -->
                                            <div x-show="page === 6" style="display: none;" class="space-y-4">
                                                <div class="border-b border-slate-200 pb-2">
                                                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600">Slide 6: Protocol</span>
                                                    <h3 class="text-lg font-bold text-slate-900 mt-0.5">Emergency Action Principles &amp; Activating Assistance</h3>
                                                </div>
                                                <div class="space-y-3 text-xs">
                                                    <div class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 bg-slate-50">
                                                        <span class="h-7 w-7 rounded-lg bg-emerald-600 text-white font-extrabold flex items-center justify-center shrink-0">1</span>
                                                        <div>
                                                            <p class="font-bold text-slate-900">1st — ASK FOR HELP (Activate Medical Assistance &bull; "Call First")</p>
                                                            <p class="text-slate-600 mt-0.5">Direct a specific bystander: <em>"You in the blue shirt, call DRR and get an AED immediately!"</em></p>
                                                            <div class="mt-2 flex flex-wrap gap-2 text-[11px] font-bold text-slate-800">
                                                                <span class="bg-amber-100 text-amber-900 px-2 py-0.5 rounded border border-amber-300">📞 DRR Hotline: 09106226622</span>
                                                                <span class="bg-amber-100 text-amber-900 px-2 py-0.5 rounded border border-amber-300">📞 Secondary: 09158486678</span>
                                                                <span class="bg-rose-100 text-rose-900 px-2 py-0.5 rounded border border-rose-300">🚨 National Emergency: 911</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 bg-slate-50">
                                                        <span class="h-7 w-7 rounded-lg bg-sky-600 text-white font-extrabold flex items-center justify-center shrink-0">2</span>
                                                        <div>
                                                            <p class="font-bold text-slate-900">2nd — Survey the Scene (Scene Safety)</p>
                                                            <p class="text-slate-600 mt-0.5">Check for live electrical wires, fire, falling debris, toxic fumes, or structural danger before approaching.</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 bg-slate-50">
                                                        <span class="h-7 w-7 rounded-lg bg-purple-600 text-white font-extrabold flex items-center justify-center shrink-0">3</span>
                                                        <div>
                                                            <p class="font-bold text-slate-900">3rd — Intervene &amp; Do No Further Harm</p>
                                                            <p class="text-slate-600 mt-0.5">Report WHAT happened, LOCATION, NUMBER of persons injured, and EXTENT of injury. <strong>You must identify yourself and drop the phone last!</strong></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- SLIDE 7: Hands-Only CPR Technique -->
                                            <div x-show="page === 7" style="display: none;" class="space-y-4">
                                                <div class="border-b border-slate-200 pb-2">
                                                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600">Slide 7: Practical Technique</span>
                                                    <h3 class="text-lg font-bold text-slate-900 mt-0.5">How to Perform Chest-Compression-Only CPR</h3>
                                                </div>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                                    <div class="p-4 rounded-xl border border-orange-200 bg-orange-50/40 space-y-2">
                                                        <span class="font-bold text-orange-800 text-sm block">1. Hand &amp; Body Position</span>
                                                        <ul class="space-y-1 list-disc list-inside text-slate-700">
                                                            <li>Place the <strong>heel of one hand</strong> in the center of the chest on the breastbone.</li>
                                                            <li>Interlace fingers of your second hand on top.</li>
                                                            <li>Kneel close to victim's side with <strong>shoulders positioned directly over hands</strong> and elbows locked straight.</li>
                                                        </ul>
                                                    </div>
                                                    <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/40 space-y-2">
                                                        <span class="font-bold text-emerald-800 text-sm block">2. Compression Rate &amp; Depth</span>
                                                        <ul class="space-y-1 list-disc list-inside text-slate-700">
                                                            <li><strong>Rate:</strong> Push at <strong>100–120 Compressions per Minute</strong> (Beat of "Stayin' Alive").</li>
                                                            <li><strong>Depth:</strong> Push down at least <strong>2 to 2.4 inches (5–6 cm)</strong>.</li>
                                                            <li><strong>Recoil:</strong> Take all weight off chest between compressions to allow complete heart refill.</li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <!-- Interactive Mid-Presentation Checkpoint on Slide 7 -->
                                                <div class="p-4 rounded-xl border border-amber-300 bg-amber-50 space-y-3">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs font-bold uppercase tracking-wider text-amber-900">⚡ Checkpoint Question (Slide 7 of 8)</span>
                                                        <span x-show="docQuizPassed" class="text-xs font-bold text-emerald-700">✓ Completed</span>
                                                    </div>
                                                    <p class="text-xs font-bold text-slate-900">What is the optimal chest compression rate and depth for an adult cardiac arrest victim?</p>
                                                    <div class="space-y-1.5 text-xs">
                                                        <label class="flex items-center gap-2 p-2.5 rounded-lg border bg-white cursor-pointer"
                                                               :class="docAnswer === 'correct' ? 'border-emerald-500 font-bold' : 'border-slate-200'">
                                                            <input type="radio" name="cpr_quiz_{{ $mat->id }}" value="correct" x-model="docAnswer" class="text-emerald-600 focus:ring-emerald-500">
                                                            <span>100–120 compressions per minute at 2–2.4 inches depth with full recoil.</span>
                                                        </label>
                                                        <label class="flex items-center gap-2 p-2.5 rounded-lg border bg-white cursor-pointer"
                                                               :class="docAnswer === 'wrong' ? 'border-rose-300' : 'border-slate-200'">
                                                            <input type="radio" name="cpr_quiz_{{ $mat->id }}" value="wrong" x-model="docAnswer" class="text-emerald-600 focus:ring-emerald-500">
                                                            <span>60 compressions per minute at 1 inch depth.</span>
                                                        </label>
                                                    </div>
                                                    <p x-show="docFeedback" x-text="docFeedback" class="text-xs font-bold text-rose-600"></p>
                                                    <div class="pt-1 flex justify-end">
                                                        <button type="button" @click="submitSlideQuiz()" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                                                            Confirm Checkpoint Answer
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- SLIDE 8: Gasping Protocol & Summary -->
                                            <div x-show="page === 8" style="display: none;" class="space-y-4">
                                                <div class="border-b border-slate-200 pb-2">
                                                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600">Slide 8: Key Takeaways &amp; Rescuer Teamwork</span>
                                                    <h3 class="text-lg font-bold text-slate-900 mt-0.5">Critical Reminders for School Responders</h3>
                                                </div>
                                                <div class="space-y-3 text-xs">
                                                    <div class="p-3.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-950 space-y-1">
                                                        <p class="font-bold text-sm">⚠️ Agonal Gasping Protocol</p>
                                                        <p>Gasping is NOT normal breathing — it is a reflex sign of cardiac arrest. <strong>DO NOT STOP chest compressions if the victim gasps!</strong> Continue hard and fast compressions without hesitation.</p>
                                                    </div>
                                                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 space-y-1 text-slate-700">
                                                        <p class="font-bold text-slate-900">Switch Rescuers Every 1–2 Minutes</p>
                                                        <p>Chest compressions are physically demanding. Rescuer fatigue causes compression depth to shallow within 2 minutes. Coach a colleague or bystander to take over with zero interruption.</p>
                                                    </div>
                                                    <div class="p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-950 space-y-1">
                                                        <p class="font-bold text-emerald-900">Don't Worry About Breaking Ribs — Keep Going!</p>
                                                        <p>A cracked rib can heal; an un-resuscitated patient will not survive. Doing your best with continuous chest compressions gives the victim the best chance at life.</p>
                                                    </div>
                                                </div>

                                                <div class="pt-3 p-4 rounded-xl bg-emerald-100/70 border border-emerald-300 flex items-center justify-between gap-3">
                                                    <div class="flex items-center gap-2 text-emerald-900 font-bold text-xs">
                                                        <span>✓ You have completed the 8 slide lessons from this official deck.</span>
                                                    </div>
                                                    <button type="button" @click="markMaterialCompleted({{ $mat->id }}, '{{ $interactUrl }}'); docQuizPassed = true;"
                                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs cursor-pointer">
                                                        <span>Mark as Complete</span>
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Document / Slide Navigation Footer -->
                                            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                                                <button type="button" @click="prevSlide()" :disabled="page === 1"
                                                        class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40 cursor-pointer">
                                                    &larr; Previous Slide
                                                </button>

                                                <button type="button" x-show="page < totalPages" @click="nextSlide()"
                                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                                                    <span>Next Slide &rarr;</span>
                                                </button>

                                                <span x-show="page === totalPages" class="text-xs font-bold text-emerald-700">
                                                    ✓ Final Slide Reached
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                @if ($assessment)
                    <section id="module-quiz" class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-5">
                        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 pb-4">
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-100">
                                    Final Module Quiz &amp; Activity
                                </span>
                                <h3 class="mt-2 text-lg font-bold text-slate-900">{{ $assessment->title }}</h3>
                                <p class="mt-1 text-xs text-slate-600">
                                    {{ $assessment->description ?? 'Answer the questions below after completing all video and document presentations above.' }}
                                </p>
                            </div>
                            <div class="text-right text-xs text-slate-600">
                                <div>Passing: <strong>{{ $assessment->passing_score }}%</strong></div>
                                <div>Attempts left: <strong>{{ $quizAttemptsLeft }}</strong></div>
                            </div>
                        </div>

                        <!-- Locked Notice Until All Videos & Files Are Played/Reviewed -->
                        <div x-show="!allMaterialsCompleted"
                             class="rounded-2xl border border-amber-200 bg-amber-50/80 p-6 text-center space-y-2">
                            <div class="mx-auto w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <h4 class="text-sm font-bold text-amber-950">Complete All Module Videos &amp; Files First</h4>
                            <p class="text-xs text-amber-800 max-w-md mx-auto">
                                Play the training video and review all module files above (including their midpoint knowledge checks) to unlock this final Module Quiz.
                                (<span class="font-bold" x-text="completedMaterials.length"></span> of <span class="font-bold" x-text="totalMaterials"></span> completed)
                            </p>
                        </div>

                        <div x-show="allMaterialsCompleted" class="space-y-5">

                        @if ($latestQuizAttempt)
                            <div class="rounded-xl border p-4 {{ $latestQuizAttempt->result === 'passed' ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-rose-200 bg-rose-50 text-rose-900' }}">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div>
                                        <h4 class="font-bold">Your latest quiz result</h4>
                                        <p class="mt-1 text-sm">
                                            Score {{ number_format((float) $latestQuizAttempt->score, 2) }} / {{ number_format($latestQuizAttempt->total_points, 2) }} points
                                            ({{ number_format($latestQuizAttempt->percentage, 2) }}%).
                                            Passing score: {{ $assessment->passing_score }}%.
                                        </p>
                                    </div>
                                    <span class="text-xs font-bold uppercase">
                                        {{ ucfirst($latestQuizAttempt->result) }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs opacity-80">
                                    Attempt #{{ $latestQuizAttempt->attempt_number }}
                                    @if ($latestQuizAttempt->submitted_at)
                                        · Submitted {{ $latestQuizAttempt->submitted_at->format('M d, Y h:i A') }}
                                    @endif
                                </p>
                            </div>
                        @endif

                        @if ($latestQuizAttempt?->result === 'passed')
                            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                                This module is complete. Your score is shown above.
                                @if ($hasCertification)
                                    <a class="ml-1 font-bold underline" href="{{ route('teacher.assessments.certificate', $workshop) }}" target="_blank">View certificate</a>
                                @endif
                            </div>
                        @elseif (! $assessment->is_published)
                            <p class="text-sm text-slate-500">This quiz is not available yet.</p>
                        @elseif (! $canAttemptQuiz)
                            <p class="text-sm text-rose-700">
                                {{ $quizAttemptsLeft > 0 ? 'Complete the previous required modules before taking this quiz.' : 'No attempts remain. Contact your trainer for support.' }}
                            </p>
                        @else
                            <form method="POST" action="{{ route('teacher.learning.module.assessment.submit', [$workshop, $module]) }}" class="space-y-5">
                                @csrf
                                @foreach ($questions as $index => $question)
                                    <fieldset class="rounded-xl border border-slate-200 p-4">
                                        <legend class="px-1 text-sm font-semibold text-slate-900">
                                            {{ $index + 1 }}. {{ $question->question_text }}
                                            <span class="text-xs font-normal text-slate-500">({{ $question->points }} pts)</span>
                                        </legend>
                                        @if ($question->question_type === 'fill_blank')
                                            <input type="text" name="answers[{{ $question->id }}]" maxlength="1000" required
                                                value="{{ old('answers.' . $question->id) }}"
                                                placeholder="Type your answer"
                                                class="mt-3 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                        @else
                                            <div class="mt-3 space-y-2">
                                                @foreach ($question->choices as $choice)
                                                    <label class="flex items-start gap-2 rounded-lg border border-slate-200 p-3 text-sm hover:bg-slate-50">
                                                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $choice->id }}"
                                                            @checked((string) old('answers.' . $question->id) === (string) $choice->id)
                                                            required class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                                        <span>{{ $choice->choice_text }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif
                                    </fieldset>
                                @endforeach

                                @error('answers')
                                    <p class="text-sm text-rose-600">{{ $message }}</p>
                                @enderror
                                <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">
                                    {{ $latestQuizAttempt ? 'Retake Quiz' : 'Submit Quiz' }}
                                </button>
                            </form>
                        @endif
                        </div>
                    </section>
                @else
                    <section id="module-quiz" class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-900">
                        The required quiz for this module is being prepared. Contact your trainer before continuing.
                    </section>
                @endif

                <!-- Bottom Navigation / Complete Module Action Bar -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        @if ($previousModule)
                            <a href="{{ route('teacher.learning.module', [$workshop, $previousModule]) }}"
                               class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 px-3 py-2 rounded-xl hover:bg-slate-100 transition-colors">
                                &larr; Previous: Module {{ $previousModule->sequence }}
                            </a>
                        @else
                            <span class="text-xs text-slate-400">First Module</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        @if ($currentProgress && $currentProgress->status === 'completed')
                            @if ($nextModule)
                                <a href="{{ route('teacher.learning.module', [$workshop, $nextModule]) }}"
                                   class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white px-5 py-2.5 transition-colors shadow-xs">
                                    Next: Module {{ $nextModule->sequence }} &rarr;
                                </a>
                            @else
                                <a href="{{ route('teacher.assessments.final', $workshop) }}"
                                   class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white px-5 py-2.5 transition-colors shadow-xs">
                                    Proceed to Final Examination &rarr;
                                </a>
                            @endif
                        @elseif ($assessment)
                            <a href="#module-quiz"
                               class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white px-5 py-2.5 transition-colors shadow-xs">
                                Pass Module Quiz to Continue &darr;
                            </a>
                        @else
                            <span class="text-xs font-semibold text-amber-700">Required quiz is not available yet</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
