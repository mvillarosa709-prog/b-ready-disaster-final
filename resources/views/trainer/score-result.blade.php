<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Result - B-Ready</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md text-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Assessment Completed!</h1>
        <p class="text-gray-600 mb-6">Here is your total score:</p>

        <div class="inline-block bg-blue-50 border border-blue-200 rounded-full px-8 py-4 mb-8">
            <span class="text-5xl font-extrabold text-blue-600">{{ $score }}</span>
            <span class="text-2xl text-gray-500"> / {{ $totalQuestions }}</span>
        </div>

        <div class="text-left space-y-4 mb-8">
            <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Answer Breakdown:</h2>
            @foreach($detailedResults as $index => $res)
                <div class="p-3 rounded {{ $res['is_correct'] ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                    <p class="font-medium text-sm text-gray-800">Q{{ $index + 1 }}: {{ $res['question'] }}</p>
                    <p class="text-xs mt-1">
                        Your Answer: <span class="font-semibold">{{ $res['selected_choice'] }}</span>
                        @if($res['is_correct'])
                            <span class="text-green-600 font-bold ml-2">✓ Correct</span>
                        @else
                            <span class="text-red-600 font-bold ml-2">✗ Incorrect</span>
                        @endif
                    </p>
                </div>
            @endforeach
        </div>

        <div>
            <a href="/trainer/take-assessment" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 text-sm">Retake Assessment</a>
            <a href="/trainer/assessments" class="ml-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 text-sm">View Questions List</a>
        </div>
    </div>
</body>
</html>