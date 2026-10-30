<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Reports - B-Ready</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow-md">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Assessment Performance Reports</h1>
                <p class="text-sm text-gray-500">Overview of teachers/trainers assessment results.</p>
            </div>
            <a href="/trainer/assessments" class="bg-gray-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700">Back to Questions</a>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg shadow-sm">
                <p class="text-sm text-blue-600 font-semibold">Total Examinees</p>
                <p class="text-3xl font-extrabold text-blue-900 mt-1">{{ $totalTakers ?? 0 }}</p>
            </div>
            <div class="bg-green-50 border border-green-200 p-4 rounded-lg shadow-sm">
                <p class="text-sm text-green-600 font-semibold">Passed Count</p>
                <p class="text-3xl font-extrabold text-green-900 mt-1">{{ $passedCount ?? 0 }}</p>
            </div>
            <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg shadow-sm">
                <p class="text-sm text-purple-600 font-semibold">Passing Rate</p>
                <p class="text-3xl font-extrabold text-purple-900 mt-1">{{ $passingRate ?? 0 }}%</p>
            </div>
        </div>

        <!-- Results Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-200 text-gray-700 text-xs uppercase tracking-wider">
                        <th class="p-3">#</th>
                        <th class="p-3">Teacher / Trainer</th>
                        <th class="p-3">Score</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Date Taken</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                    @forelse($results ?? [] as $index => $result)
                        <tr>
                            <td class="p-3">{{ $index + 1 }}</td>
                            <td class="p-3 font-medium">{{ $result->teacher_name ?? 'Trainer' }}</td>
                            <td class="p-3">{{ $result->score }} / {{ $result->total_questions }}</td>
                            <td class="p-3">
                                @if($result->is_passed)
                                    <span class="bg-green-100 text-green-800 px-2.5 py-1 rounded-full text-xs font-semibold">Passed</span>
                                @else
                                    <span class="bg-red-100 text-red-800 px-2.5 py-1 rounded-full text-xs font-semibold">Failed</span>
                                @endif
                            </td>
                            <td class="p-3 text-gray-500">{{ $result->created_at ? $result->created_at->format('M d, Y h:i A') : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500">There are no assessment results to display.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>