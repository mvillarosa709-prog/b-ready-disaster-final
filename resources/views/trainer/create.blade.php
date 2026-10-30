<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Assessment Question - B-Ready</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Add New Assessment Question (Trainer)</h1>
        
        <form action="/trainer/assessments/store" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Tab ng Upload File para sa maramihang tanong -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <label class="block text-blue-800 font-bold mb-2">Upload a CSV file for multiple questions:</label>
                <input type="file" name="assessment_file" class="w-full border border-blue-300 p-2 rounded-lg bg-white">
                <p class="text-xs text-gray-500 mt-1">You can upload a CSV file to directly save all questions.</p>
            </div>

            <div class="border-t pt-4">
                <h2 class="text-lg font-semibold mb-4 text-gray-700">Or add questions individually:</h2>
                
                <!-- Tanong -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Question Text:</label>
                    <textarea name="question_text" rows="3" class="w-full border border-gray-300 p-2 rounded-lg" placeholder="Enter the question for disaster preparedness here..."></textarea>
                </div>

                <!-- Mga Choices -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Choices & Correct Answer:</label>
                    
                    <div class="flex items-center mb-2">
                        <input type="radio" name="correct_answer" value="0" class="mr-2">
                        <input type="text" name="choices[]" class="w-full border border-gray-300 p-2 rounded-lg" placeholder="Choice A">
                    </div>
                    <div class="flex items-center mb-2">
                        <input type="radio" name="correct_answer" value="1" class="mr-2">
                        <input type="text" name="choices[]" class="w-full border border-gray-300 p-2 rounded-lg" placeholder="Choice B">
                    </div>
                    <div class="flex items-center mb-2">
                        <input type="radio" name="correct_answer" value="2" class="mr-2">
                        <input type="text" name="choices[]" class="w-full border border-gray-300 p-2 rounded-lg" placeholder="Choice C">
                    </div>
                    <div class="flex items-center mb-4">
                        <input type="radio" name="correct_answer" value="3" class="mr-2">
                        <input type="text" name="choices[]" class="w-full border border-gray-300 p-2 rounded-lg" placeholder="Choice D">
                    </div>
                </div>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save Questions</button>
        </form>
    </div>
</body>
</html>