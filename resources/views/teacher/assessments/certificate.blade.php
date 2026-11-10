<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certificate — {{ $teacher->name }} &bull; {{ $certification->certificate_number }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=playfair-display:400,600,700,900|montserrat:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .certificate-container {
                border-color: #d4af37 !important;
                box-shadow: none !important;
                page-break-inside: avoid;
            }
        }
        .font-serif-title {
            font-family: 'Playfair Display', Georgia, serif;
        }
        .font-sans-body {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-10 px-4 flex flex-col items-center justify-center font-sans-body">
    <!-- Print / Action Toolbar -->
    <div class="no-print mb-6 flex items-center gap-4">
        <button type="button" 
                onclick="window.print()" 
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print / Save as PDF
        </button>

        <a href="{{ route('teacher.workshops.show', $workshop) }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition-colors shadow-xs">
            &larr; Back to Workshop
        </a>
    </div>

    <!-- The Certificate Canvas -->
    <div class="certificate-container w-full max-w-4xl bg-white rounded-3xl p-10 sm:p-14 shadow-2xl border-12 border-double border-amber-600/40 relative overflow-hidden text-center">
        <!-- Inner Decorative Golden Border -->
        <div class="absolute inset-3 border-2 border-amber-500/30 rounded-2xl pointer-events-none"></div>

        <!-- Watermark Shield Icon -->
        <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none">
            <svg class="w-96 h-96 text-slate-900" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 2.18l7 3.12v4.7c0 4.67-3.13 9.04-7 10.18-3.87-1.14-7-5.51-7-10.18V6.3l7-3.12z"/>
            </svg>
        </div>

        <!-- Top Header Organization -->
        <div class="relative z-10 space-y-2">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-900 text-white text-[11px] font-bold tracking-widest uppercase">
                B-READY &bull; DISASTER PREPAREDNESS TRAINING PLATFORM
            </div>
            <h1 class="font-serif-title text-3xl sm:text-5xl font-black tracking-tight text-slate-900 pt-4 uppercase">
                Certificate of Completion
            </h1>
            <p class="text-xs sm:text-sm font-semibold tracking-widest uppercase text-amber-700 pt-1">
                DISASTER RISK REDUCTION & MANAGEMENT ACCREDITATION
            </p>
            <span class="inline-flex rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider {{ $certification->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                {{ $certification->status === 'approved' ? 'Approved & Signed' : 'Pending Verification' }}
            </span>
        </div>

        <!-- Awarded to -->
        <div class="relative z-10 my-8 space-y-2">
            <p class="text-xs italic text-slate-500 font-serif">This is proudly presented to</p>
            <h2 class="font-serif-title text-3xl sm:text-4xl font-extrabold text-emerald-950 underline decoration-amber-500/60 decoration-2 underline-offset-8">
                {{ $teacher->name }}
            </h2>
            <p class="text-xs font-semibold text-slate-500 pt-2 tracking-wide uppercase">
                Accredited Educator / School Disaster Coordinator
            </p>
        </div>

        <!-- Description & Course details -->
        <div class="relative z-10 max-w-2xl mx-auto space-y-3 text-xs sm:text-sm text-slate-600 leading-relaxed">
            <p>
                For successfully fulfilling all requirements, passing the rigorous competence examination, and mastering instructional methodologies in:
            </p>
            <h3 class="font-serif-title text-lg sm:text-xl font-bold text-slate-900">
                "{{ $workshop->title }}"
            </h3>
            <p class="text-xs text-slate-500">
                Course: {{ $workshop->course->title ?? 'DRR Foundation Curriculum' }} &bull; Completed on {{ $certification->certified_at->format('F d, Y') }}
            </p>
        </div>

        <!-- Certificate Badge & Seal Section -->
        <div class="relative z-10 mt-10 grid grid-cols-1 sm:grid-cols-3 gap-6 items-end pt-8 border-t border-slate-200">
            <!-- Left: Lead Trainer Signature -->
            <div class="text-center sm:text-left space-y-1">
                @if ($certification->status === 'approved')
                    <div class="h-10 border-b border-slate-400 max-w-[200px] mx-auto sm:mx-0 flex items-end justify-center sm:justify-start pb-1">
                        <span class="font-serif italic text-base text-emerald-900 font-semibold">{{ $certification->approver?->name ?? 'DRR Trainer' }}</span>
                    </div>
                    <div class="text-[11px] font-bold text-slate-800 uppercase tracking-wider">Digitally signed by {{ $certification->approver?->name ?? 'DRR Trainer' }}</div>
                    <div class="text-[10px] text-slate-400">Approved {{ $certification->approved_at?->format('F d, Y') }}</div>
                @else
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs font-semibold leading-relaxed text-amber-900 sm:max-w-[240px]">
                        Naghihintay pa ng beripikasyon at lagda mula sa DRR Trainer.
                    </div>
                @endif
            </div>

            <!-- Center: Gold Badge / Seal -->
            <div class="flex flex-col items-center justify-center">
                <div class="h-20 w-20 rounded-full bg-gradient-to-tr from-amber-600 via-amber-400 to-yellow-200 p-1 shadow-lg border-4 border-amber-600/30 flex items-center justify-center">
                    <div class="h-full w-full rounded-full bg-slate-900 text-amber-300 flex flex-col items-center justify-center text-center p-1">
                        <span class="text-[8px] font-extrabold tracking-widest uppercase">CERTIFIED</span>
                        <svg class="w-5 h-5 text-amber-400 my-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-[7px] font-bold text-slate-300 tracking-wider">B-READY</span>
                    </div>
                </div>
                <div class="text-[10px] font-extrabold text-amber-800 tracking-widest uppercase mt-2">
                    {{ $certification->badge_name }}
                </div>
            </div>

            <!-- Right: Platform Director Signature -->
            <div class="text-center sm:text-right space-y-1">
                <div class="h-10 border-b border-slate-400 max-w-[200px] mx-auto sm:ml-auto flex items-end justify-center sm:justify-end pb-1">
                    <span class="font-serif italic text-base text-slate-800 font-semibold">Dr. A. Valenzuela</span>
                </div>
                <div class="text-[11px] font-bold text-slate-800 uppercase tracking-wider">Program Director</div>
                <div class="text-[10px] text-slate-400">National DRR Council Partner</div>
            </div>
        </div>

        <!-- Bottom Metadata: Certificate Number & Verification QR placeholder -->
        <div class="relative z-10 mt-8 pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-400">
            <div>
                Verification ID: <span class="font-mono font-bold text-slate-700">{{ $certification->certificate_number }}</span>
            </div>
            <div>
                Authenticity verifiable at: <span class="font-mono text-emerald-600 font-semibold">https://bready.org/verify/{{ $certification->certificate_number }}</span>
            </div>
        </div>
    </div>
</body>
</html>
