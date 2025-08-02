<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quiz Save Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2, h3 {
            color: #333;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        .warning {
            color: orange;
        }
        pre {
            background: #f5f5f5;
            padding: 10px;
            border: 1px solid #ddd;
            white-space: pre-wrap;
            overflow-x: auto;
        }
        .test-button {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            margin: 5px;
        }
        .test-button:hover {
            background: #45a049;
        }
        .results {
            margin-top: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Quiz Save Test Tool</h1>
    
    <h2>Test Quiz Creation</h2>
    <p>This tool will test the admin quiz creation functionality.</p>
    
    <button class="test-button" onclick="testQuizSave()">Test Quiz Save</button>
    <button class="test-button" onclick="testValidation()">Test Validation</button>
    <button class="test-button" onclick="testFieldMapping()">Test Field Mapping</button>
    
    <div id="results" class="results" style="display: none;">
        <h3>Test Results</h3>
        <pre id="output"></pre>
    </div>
    
    <script>
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Test data
        const testQuizData = {
            title: 'Test Quiz ' + new Date().toLocaleString(),
            description: 'Test Description',
            program_id: '41', // Make sure this exists in your database
            module_id: '79',  // Make sure this exists in your database
            course_id: '52',  // Make sure this exists in your database
            admin_id: 1,
            time_limit: 60,
            max_attempts: 1,
            infinite_retakes: false,
            has_deadline: false,
            is_draft: true,
            status: 'draft',
            questions: [
                {
                    question_text: 'What is 2 + 2?',
                    question_type: 'multiple_choice',
                    options: ['2', '3', '4', '5'],
                    correct_answers: [2], // Index 2 = '4'
                    explanation: 'Basic arithmetic',
                    points: 1
                },
                {
                    question_text: 'Is the sky blue?',
                    question_type: 'true_false',
                    options: ['True', 'False'],
                    correct_answers: [0], // Index 0 = 'True'
                    explanation: 'The sky appears blue due to light scattering',
                    points: 1
                }
            ]
        };
        
        function showResults(message, isError = false) {
            const resultsDiv = document.getElementById('results');
            const outputPre = document.getElementById('output');
            
            resultsDiv.style.display = 'block';
            outputPre.className = isError ? 'error' : 'success';
            outputPre.textContent = message;
        }
        
        async function testQuizSave() {
            showResults('Testing quiz save...', false);
            
            try {
                const response = await fetch('/admin/quiz-generator/save-quiz', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(testQuizData)
                });
                
                const data = await response.json();
                
                let message = `Status: ${response.status}\n\n`;
                message += `Response:\n${JSON.stringify(data, null, 2)}`;
                
                showResults(message, !response.ok);
                
            } catch (error) {
                showResults(`Error: ${error.message}`, true);
            }
        }
        
        async function testValidation() {
            showResults('Testing validation with invalid data...', false);
            
            const invalidData = {
                // Missing title
                program_id: 'invalid_id',
                questions: [] // Empty questions array
            };
            
            try {
                const response = await fetch('/admin/quiz-generator/save-quiz', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(invalidData)
                });
                
                const data = await response.json();
                
                let message = `Validation Test Status: ${response.status}\n\n`;
                message += `Response:\n${JSON.stringify(data, null, 2)}`;
                
                showResults(message, false);
                
            } catch (error) {
                showResults(`Validation Test Error: ${error.message}`, true);
            }
        }
        
        async function testFieldMapping() {
            showResults('Testing different field name formats...', false);
            
            // Test with different field naming conventions
            const alternativeData = {
                quiz_title: 'Alternative Field Names Test', // Using quiz_title instead of title
                description: 'Testing alternative field names',
                program_id: '41',
                module_id: '79',
                course_id: '52',
                admin_id: 1,
                time_limit: 60,
                max_attempts: 1,
                is_draft: true,
                questions: [
                    {
                        question: 'Alternative question field test?', // Using 'question' instead of 'question_text'
                        question_type: 'multiple_choice',
                        options: ['A', 'B', 'C', 'D'],
                        correct_answer: 1, // Using correct_answer instead of correct_answers
                        explanation: 'Testing alternative field names',
                        points: 1
                    }
                ]
            };
            
            try {
                const response = await fetch('/admin/quiz-generator/save-quiz', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(alternativeData)
                });
                
                const data = await response.json();
                
                let message = `Field Mapping Test Status: ${response.status}\n\n`;
                message += `Response:\n${JSON.stringify(data, null, 2)}`;
                
                showResults(message, !response.ok);
                
            } catch (error) {
                showResults(`Field Mapping Test Error: ${error.message}`, true);
            }
        }
    </script>
</body>
</html>
