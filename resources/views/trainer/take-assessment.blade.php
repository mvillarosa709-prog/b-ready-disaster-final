<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Assessment - B-Ready</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Trainer Assessment Quiz</h1>

        <form action="/trainer/grade-assessment" method="POST">
            @csrf
            <div class="space-y-6">
                @foreach($questions as $index => $q)
                    <div class="border border-gray-200 p-4 rounded-lg bg-gray-50">
                        <p class="font-semibold text-gray-800 mb-3">
                            <span class="text-blue-600">Q{{ $index + 1 }}:</span> {{ $q->question_text }}
                        </p>
                        <div class="space-y-2 pl-4">
                            @foreach($q->choices as $choice)
                                <label class="flex items-center space-x-3 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="answers[{{ $q->id }}]" value="{{ $choice->id }}" required class="text-blue-600 focus:ring-blue-500">
                                    <span>{{ $choice->choice_text }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Submit and View Score
                </button>
            </div>
        </form>
    </div>
</body>
</html>