<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">Log Classroom Implementation</x-slot>
    <x-slot name="header">New Classroom Report</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back Navigation -->
        <div>
            <a href="{{ route('teacher.implementations.index') }}" 
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-indigo-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Implementation Reports
            </a>
        </div>

        @if ($noCertifications)
            <!-- Uncertified Alert Banner -->
            <div class="bg-white rounded-2xl border border-amber-200 p-8 shadow-xs text-center space-y-4">
                <div class="mx-auto h-12 w-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Certification Prerequisite Required</h3>
                <p class="text-xs text-slate-600 max-w-md mx-auto">
                    To maintain educational fidelity, classroom implementations can only be logged for workshops where you have passed the final assessment and earned official DRR certification.
                </p>
                <div class="pt-2">
                    <a href="{{ route('teacher.workshops.my') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 shadow-xs transition-colors">
                        Go to My Workshops
                    </a>
                </div>
            </div>
        @else
            <!-- Implementation Form -->
            <form method="POST" 
                  action="{{ route('teacher.implementations.store') }}" 
                  enctype="multipart/form-data" 
                  class="space-y-6"
                  x-data="studentMatrix()">
                @csrf

                <!-- Section 1: Workshop & Session Context -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-5">
                    <div class="border-b border-slate-100 pb-3">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Section 1</span>
                        <h3 class="text-base font-bold text-slate-900">Implementation Details</h3>
                        <p class="text-xs text-slate-500">Select the accredited workshop and date you rolled out the lesson to students.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Workshop Selection -->
                        <div>
                            <label for="workshop_id" class="block text-xs font-bold text-slate-700 mb-1">
                                Certified Workshop <span class="text-rose-500">*</span>
                            </label>
                            <select id="workshop_id" 
                                    name="workshop_id" 
                                    required
                                    class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-2.5 text-xs text-slate-900 focus:border-indigo-500 focus:bg-white focus:outline-hidden focus:ring-1 focus:ring-indigo-500">
                                @foreach ($eligibleWorkshops as $w)
                                    <option value="{{ $w->id }}" {{ old('workshop_id', $selectedWorkshop->id ?? '') == $w->id ? 'selected' : '' }}>
                                        {{ $w->title }} ({{ $w->classroomPackage->title ?? 'Workshop Repository' }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="classroom_package_id" value="{{ $selectedWorkshop->classroomPackage->id ?? 1 }}">
                            @error('workshop_id')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Implementation Date -->
                        <div>
                            <label for="implementation_date" class="block text-xs font-bold text-slate-700 mb-1">
                                Implementation Date <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" 
                                   id="implementation_date" 
                                   name="implementation_date" 
                                   value="{{ old('implementation_date', date('Y-m-d')) }}"
                                   max="{{ date('Y-m-d') }}"
                                   required
                                   class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-2.5 text-xs text-slate-900 focus:border-indigo-500 focus:bg-white focus:outline-hidden focus:ring-1 focus:ring-indigo-500">
                            @error('implementation_date')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Qualitative Remarks & Teacher Reflection -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-5">
                    <div class="border-b border-slate-100 pb-3">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Section 2</span>
                        <h3 class="text-base font-bold text-slate-900">Qualitative Reflection & Observations</h3>
                        <p class="text-xs text-slate-500">Record pedagogical insights, student engagement, and implementation hurdles.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="teacher_reflection" class="block text-xs font-bold text-slate-700 mb-1">
                                Teacher Reflection & Drill Outcomes
                            </label>
                            <textarea id="teacher_reflection" 
                                      name="teacher_reflection" 
                                      rows="3"
                                      placeholder="Reflect on student engagement, drill execution, understanding of evacuation protocols, etc..."
                                      class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-xs text-slate-900 focus:border-indigo-500 focus:bg-white focus:outline-hidden focus:ring-1 focus:ring-indigo-500">{{ old('teacher_reflection') }}</textarea>
                            @error('teacher_reflection')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="remarks" class="block text-xs font-bold text-slate-700 mb-1">
                                Additional Notes or Resource Recommendations
                            </label>
                            <textarea id="remarks" 
                                      name="remarks" 
                                      rows="2"
                                      placeholder="Any notes for the trainer or school administration..."
                                      class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-xs text-slate-900 focus:border-indigo-500 focus:bg-white focus:outline-hidden focus:ring-1 focus:ring-indigo-500">{{ old('remarks') }}</textarea>
                        </div>

                        <div>
                            <label for="supporting_record" class="block text-xs font-bold text-slate-700 mb-1">
                                Supporting Document or Drill Photo (Optional)
                            </label>
                            <input type="file" 
                                   id="supporting_record" 
                                   name="supporting_record"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="text-[11px] text-slate-400 mt-1">PDF, JPG, PNG, DOC up to 10MB.</p>
                            @error('supporting_record')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 3: Dynamic Student Results Matrix -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Section 3</span>
                            <h3 class="text-base font-bold text-slate-900">Dynamic Student Assessment Matrix</h3>
                            <p class="text-xs text-slate-500">Add individual student scores. Aggregates are computed automatically.</p>
                        </div>

                        <!-- Live Metrics Display -->
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-slate-100 text-xs font-semibold text-slate-700">
                                Students: <strong class="text-slate-900" x-text="students.length"></strong>
                            </span>
                            <span class="px-3 py-1 rounded-xl bg-emerald-50 text-xs font-semibold text-emerald-800 border border-emerald-200">
                                Passed: <strong class="text-emerald-950" x-text="computedPassedCount()"></strong>
                            </span>
                            <span class="px-3 py-1 rounded-xl bg-indigo-50 text-xs font-semibold text-indigo-800 border border-indigo-200">
                                Avg: <strong class="text-indigo-950" x-text="computedAverage() + '%'"></strong>
                            </span>
                        </div>
                    </div>

                    <!-- Students Input Table -->
                    <div class="overflow-x-auto border border-slate-200 rounded-xl">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="p-3 w-12 text-center">#</th>
                                    <th class="p-3">Student Identifier (Name / LRN / Anonymous ID) <span class="text-rose-500">*</span></th>
                                    <th class="p-3 w-32">Score <span class="text-rose-500">*</span></th>
                                    <th class="p-3 w-28">Total Questions <span class="text-rose-500">*</span></th>
                                    <th class="p-3 w-28 text-center">Percentage</th>
                                    <th class="p-3 w-24 text-center">Status</th>
                                    <th class="p-3 w-16 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="(student, index) in students" :key="index">
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="p-3 text-center text-slate-400 font-bold" x-text="index + 1"></td>
                                        <td class="p-3">
                                            <input type="text" 
                                                   :name="'students[' + index + '][student_identifier]'"
                                                   x-model="student.student_identifier"
                                                   placeholder="e.g. Student-101 / Anonymous-01"
                                                   required
                                                   class="block w-full rounded-lg border border-slate-200 p-2 text-xs text-slate-900 focus:border-indigo-500 focus:outline-hidden">
                                        </td>
                                        <td class="p-3">
                                            <input type="number" 
                                                   :name="'students[' + index + '][score]'"
                                                   x-model.number="student.score"
                                                   step="0.5"
                                                   min="0"
                                                   required
                                                   class="block w-full rounded-lg border border-slate-200 p-2 text-xs text-slate-900 focus:border-indigo-500 focus:outline-hidden">
                                        </td>
                                        <td class="p-3">
                                            <input type="number" 
                                                   :name="'students[' + index + '][total_questions]'"
                                                   x-model.number="student.total_questions"
                                                   step="1"
                                                   min="1"
                                                   required
                                                   class="block w-full rounded-lg border border-slate-200 p-2 text-xs text-slate-900 focus:border-indigo-500 focus:outline-hidden">
                                            <input type="hidden" :name="'students[' + index + '][max_score]'" :value="student.total_questions">
                                        </td>
                                        <td class="p-3 text-center font-bold text-slate-800" x-text="calculatePercentage(student) + '%'"></td>
                                        <td class="p-3 text-center">
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase"
                                                  :class="calculatePercentage(student) >= 70 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'"
                                                  x-text="calculatePercentage(student) >= 70 ? 'Pass' : 'Fail'">
                                            </span>
                                        </td>
                                        <td class="p-3 text-center">
                                            <button type="button" 
                                                    @click="removeRow(index)"
                                                    :disabled="students.length <= 1"
                                                    class="text-slate-400 hover:text-rose-600 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Add Row Button -->
                    <div class="flex items-center justify-between pt-2">
                        <button type="button" 
                                @click="addRow()"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Student Row
                        </button>

                        <button type="button" 
                                @click="addSampleRows(3)"
                                class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                            + Add 3 More Rows
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('teacher.implementations.index') }}" 
                       class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                        Cancel
                    </a>

                    <button type="submit" 
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-6 py-3 shadow-xs transition-colors">
                        Save Implementation &amp; Generate Analytics
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Alpine.js Matrix Script -->
            <script>
                function studentMatrix() {
                    return {
                        students: [
                            { student_identifier: 'Student 101', score: 85, total_questions: 100 },
                            { student_identifier: 'Student 102', score: 72, total_questions: 100 },
                            { student_identifier: 'Student 103', score: 94, total_questions: 100 },
                            { student_identifier: 'Student 104', score: 65, total_questions: 100 }
                        ],
                        addRow() {
                            const nextNum = this.students.length + 1;
                            this.students.push({
                                student_identifier: 'Student ' + nextNum,
                                score: 80,
                                total_questions: 100
                            });
                        },
                        addSampleRows(count) {
                            for (let i = 0; i < count; i++) {
                                this.addRow();
                            }
                        },
                        removeRow(index) {
                            if (this.students.length > 1) {
                                this.students.splice(index, 1);
                            }
                        },
                        calculatePercentage(student) {
                            const total = student.total_questions || student.max_score || 0;
                            if (!total || total <= 0) return 0;
                            const pct = (student.score / total) * 100;
                            return Math.round(pct * 10) / 10;
                        },
                        computedPassedCount() {
                            return this.students.filter(s => this.calculatePercentage(s) >= 70).length;
                        },
                        computedAverage() {
                            if (this.students.length === 0) return 0;
                            const sum = this.students.reduce((acc, s) => acc + this.calculatePercentage(s), 0);
                            return Math.round((sum / this.students.length) * 10) / 10;
                        }
                    }
                }
            </script>
        @endif
    </div>
</x-dynamic-component>
