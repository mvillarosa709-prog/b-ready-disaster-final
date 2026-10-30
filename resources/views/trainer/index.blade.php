<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Questions List - B-Ready</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Assessment Questions List</h1>
            <a href="/trainer/assessments/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">Upload another CSV</a>
        </div>

        @if($questions->isEmpty())
            <p class="text-gray-500">No questions available in the database.</p>
        @else
            <div class="space-y-6">
                @foreach($questions as $index => $q)
                    <div class="border border-gray-200 p-4 rounded-lg bg-gray-50">
                        <p class="font-semibold text-gray-800 mb-2">
                            <span class="text-blue-600">Q{{ $index + 1 }}:</span> {{ $q->question_text }}
                        </p>
                        <ul class="space-y-1 pl-4">
                            @foreach($q->choices as $choice)
                                <li class="text-sm {{ $choice->is_correct ? 'text-green-600 font-bold' : 'text-gray-600' }}">
                                    - {{ $choice->choice_text }} 
                                    @if($choice->is_correct)
                                        <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">Tamang Sagot</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>