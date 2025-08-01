<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Generator Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; }
        .test-section { background: #f5f5f5; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .loading { color: orange; font-style: italic; }
        #results { background: white; padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-top: 10px; }
        textarea { width: 100%; height: 150px; margin: 10px 0; }
        input[type="file"] { margin: 10px 0; }
        .question { background: #e9ecef; padding: 10px; margin: 10px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Quiz Generator Test Page</h1>
        
        <!-- Authentication Test -->
        <div class="test-section">
            <h2>1. Authentication Test</h2>
            <button class="btn" onclick="testAuth()">Test Authentication</button>
            <div id="auth-result"></div>
        </div>

        <!-- API Test -->
        <div class="test-section">
            <h2>2. API Connection Test</h2>
            <button class="btn" onclick="testAPI()">Test AI API</button>
            <div id="api-result"></div>
        </div>

        <!-- Quiz Generation Test -->
        <div class="test-section">
            <h2>3. Quiz Generation Test</h2>
            <div>
                <label>Test Content:</label>
                <textarea id="testContent">Machine Design Fundamentals

Machine design is the process of creating mechanical components and systems that meet specific requirements. It involves understanding materials, stresses, and manufacturing processes.

Key Concepts:
1. Stress Analysis - Understanding how forces affect materials
2. Factor of Safety - Ensuring designs can handle unexpected loads
3. Material Selection - Choosing appropriate materials for applications
4. Fatigue Analysis - Considering repeated loading effects

Types of stress include tension, compression, shear, and torsion. Each requires different analysis methods and safety considerations.

The design process involves problem definition, conceptual design, detailed design, and testing validation.</textarea>
            </div>
            
            <div>
                <label>Number of Questions:</label>
                <input type="number" id="numQuestions" value="5" min="1" max="20">
            </div>
            
            <div>
                <label>Question Type:</label>
                <select id="questionType">
                    <option value="mixed">Mixed</option>
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="true_false">True/False</option>
                </select>
            </div>
            
            <div>
                <label>Upload File (optional):</label>
                <input type="file" id="testFile" accept=".txt,.pdf,.doc,.docx">
            </div>
            
            <button class="btn" onclick="generateQuiz()">Generate Quiz</button>
            <div id="quiz-result"></div>
        </div>

        <!-- Results Display -->
        <div class="test-section">
            <h2>4. Generated Questions</h2>
            <div id="questions-display"></div>
        </div>
    </div>

    <script>
        // Set up CSRF token
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function testAuth() {
            const resultDiv = document.getElementById('auth-result');
            resultDiv.innerHTML = '<div class="loading">Testing authentication...</div>';
            
            try {
                // Set session for testing
                await fetch('/test-set-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
                    },
                    body: JSON.stringify({
                        professor_id: 8,
                        user_role: 'professor',
                        logged_in: true
                    })
                });
                
                resultDiv.innerHTML = '<div class="success">✅ Authentication configured</div>';
            } catch (error) {
                resultDiv.innerHTML = '<div class="error">❌ Auth test failed: ' + error.message + '</div>';
            }
        }

        async function testAPI() {
            const resultDiv = document.getElementById('api-result');
            resultDiv.innerHTML = '<div class="loading">Testing API connection...</div>';
            
            try {
                const response = await fetch('/test-ai-connection');
                const data = await response.json();
                
                if (data.success) {
                    resultDiv.innerHTML = '<div class="success">✅ API Connection successful</div>';
                } else {
                    resultDiv.innerHTML = '<div class="error">❌ API Connection failed: ' + data.message + '</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="error">❌ API test failed: ' + error.message + '</div>';
            }
        }

        async function generateQuiz() {
            const resultDiv = document.getElementById('quiz-result');
            const questionsDiv = document.getElementById('questions-display');
            
            resultDiv.innerHTML = '<div class="loading">Generating quiz questions...</div>';
            questionsDiv.innerHTML = '';
            
            try {
                const fileInput = document.getElementById('testFile');
                const content = document.getElementById('testContent').value;
                const numQuestions = document.getElementById('numQuestions').value;
                const questionType = document.getElementById('questionType').value;
                
                const formData = new FormData();
                
                // If file is selected, use it; otherwise create a text file from content
                if (fileInput.files.length > 0) {
                    formData.append('file', fileInput.files[0]);
                } else {
                    // Create a blob from the text content
                    const blob = new Blob([content], { type: 'text/plain' });
                    formData.append('file', blob, 'test_content.txt');
                }
                
                formData.append('num_questions', numQuestions);
                formData.append('question_type', questionType);
                formData.append('_token', window.csrfToken);
                
                const response = await fetch('/professor/quiz-generator/generate-ai-questions', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.success && data.questions) {
                    resultDiv.innerHTML = '<div class="success">✅ Quiz generated successfully! ' + data.questions.length + ' questions created.</div>';
                    
                    let questionsHtml = '';
                    data.questions.forEach((question, index) => {
                        questionsHtml += `
                            <div class="question">
                                <h4>Question ${index + 1} (${question.type})</h4>
                                <p><strong>Q:</strong> ${question.question}</p>
                                <p><strong>Answer:</strong> ${question.correct_answer}</p>
                                ${question.type === 'multiple_choice' && question.options ? 
                                    '<p><strong>Options:</strong> ' + Object.entries(question.options).map(([key, value]) => `${key}. ${value}`).join(' | ') + '</p>' 
                                    : ''}
                                ${question.explanation ? '<p><strong>Explanation:</strong> ' + question.explanation + '</p>' : ''}
                            </div>
                        `;
                    });
                    
                    questionsDiv.innerHTML = questionsHtml;
                } else {
                    resultDiv.innerHTML = '<div class="error">❌ Quiz generation failed: ' + (data.message || 'Unknown error') + '</div>';
                }
                
            } catch (error) {
                resultDiv.innerHTML = '<div class="error">❌ Quiz generation failed: ' + error.message + '</div>';
            }
        }

        // Auto-run auth test on load
        window.addEventListener('load', function() {
            testAuth();
        });
    </script>
</body>
</html>
