<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="app-url" content="<?php echo e(url('/')); ?>">
    <meta name="user-id" content="<?php echo e(session('user_id')); ?>">
    <title><?php echo e($quiz->quiz_title); ?> - Quiz - A.R.T.C</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #f8fafc !important;
            height: 100vh !important;
            overflow: hidden !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .quiz-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .quiz-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            flex-shrink: 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            position: relative;
        }
        
        .quiz-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 25%, transparent 25%), 
                        linear-gradient(-45deg, rgba(255,255,255,0.1) 25%, transparent 25%), 
                        linear-gradient(45deg, transparent 75%, rgba(255,255,255,0.1) 75%), 
                        linear-gradient(-45deg, transparent 75%, rgba(255,255,255,0.1) 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            pointer-events: none;
        }
        
        .quiz-body {
            flex: 1;
            display: flex;
            overflow: hidden;
        }
        
        .questions-panel {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
            background: white;
            position: relative;
        }
        
        .sidebar-panel {
            width: 320px;
            background: #f8f9fa;
            border-left: 1px solid #dee2e6;
            padding: 1.5rem;
            overflow-y: auto;
            flex-shrink: 0;
        }
        
        .question-card {
            background: white;
            border: 2px solid #e3e6f0;
            border-radius: 12px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .question-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }
        
        .question-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .question-text {
            font-size: 1.2rem;
            font-weight: 500;
            margin-bottom: 2rem;
            line-height: 1.7;
            color: #2d3748;
        }
        
        .answer-option {
            margin-bottom: 1.2rem;
        }
        
        .answer-option input[type="radio"] {
            transform: scale(1.3);
            margin-right: 1rem;
            accent-color: #667eea;
        }
        
        .answer-option label {
            font-size: 1.05rem;
            cursor: pointer;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: block;
            margin-left: 2rem;
            border: 2px solid #e3e6f0;
            background: #fafbfc;
        }
        
        .answer-option label:hover {
            background: #f1f5f9;
            border-color: #667eea;
            transform: translateX(5px);
        }
        
        .answer-option input[type="radio"]:checked + label {
            background: linear-gradient(135deg, #e7f3ff 0%, #dbeafe 100%);
            border-color: #667eea;
            color: #1e40af;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }
        
        .timer-display {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 2px solid #e3e6f0;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .timer-display.warning {
            border-color: #f59e0b;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            animation: pulse-warning 2s infinite;
        }
        
        .timer-display.danger {
            border-color: #ef4444;
            background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
            animation: pulse-danger 1s infinite;
        }
        
        @keyframes pulse-warning {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        
        @keyframes pulse-danger {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .timer-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1e40af;
        }
        
        .progress-overview {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 2px solid #e3e6f0;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .question-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.75rem;
            margin-top: 1rem;
        }
        
        .question-btn {
            aspect-ratio: 1;
            border: 2px solid #dee2e6;
            background: white;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .question-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        
        .question-btn:hover::before {
            left: 100%;
        }
        
        .question-btn:hover {
            border-color: #667eea;
            background: #f8fafc;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }
        
        .question-btn.answered {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-color: #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .question-btn.current {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            animation: pulse-current 2s infinite;
        }
        
        @keyframes pulse-current {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .quiz-actions {
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-top: 2px solid #e3e6f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.08);
        }
        
        .navigation-buttons {
            display: flex;
            gap: 1rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
        }
        
        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }
        
        .btn-outline-primary {
            border-color: #667eea;
            color: #667eea;
        }
        
        .btn-outline-primary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-color: #10b981;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        /* Mobile Responsive Styles */
        @media (max-width: 992px) {
            .quiz-body {
                flex-direction: column;
            }
            
            .sidebar-panel {
                width: 100%;
                order: -1;
                max-height: 280px;
                border-left: none;
                border-bottom: 2px solid #dee2e6;
                padding: 1rem;
            }
            
            .questions-panel {
                padding: 1.5rem;
            }
            
            .question-grid {
                grid-template-columns: repeat(8, 1fr);
                gap: 0.5rem;
            }
            
            .quiz-header {
                padding: 1rem;
            }
            
            .quiz-header .d-flex {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
            
            .quiz-actions {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }
            
            .navigation-buttons {
                width: 100%;
                justify-content: space-between;
            }
            
            .btn {
                flex: 1;
                margin: 0 0.25rem;
            }
        }
        
        @media (max-width: 576px) {
            .quiz-header {
                padding: 0.75rem;
            }
            
            .questions-panel {
                padding: 1rem;
            }
            
            .question-card {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }
            
            .question-text {
                font-size: 1rem;
                margin-bottom: 1.5rem;
            }
            
            .answer-option label {
                margin-left: 1rem;
                padding: 0.75rem 1rem;
                font-size: 0.95rem;
            }
            
            .answer-option input[type="radio"] {
                transform: scale(1.2);
                margin-right: 0.75rem;
            }
            
            .question-grid {
                grid-template-columns: repeat(6, 1fr);
            }
            
            .timer-display, .progress-overview {
                padding: 1rem;
            }
            
            .quiz-actions {
                padding: 0.75rem;
            }
            
            .btn {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
        }
        
        /* Animation for smooth transitions */
        .question-card {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Scrollbar Styling */
        .questions-panel::-webkit-scrollbar,
        .sidebar-panel::-webkit-scrollbar {
            width: 8px;
        }
        
        .questions-panel::-webkit-scrollbar-track,
        .sidebar-panel::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .questions-panel::-webkit-scrollbar-thumb,
        .sidebar-panel::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        .questions-panel::-webkit-scrollbar-thumb:hover,
        .sidebar-panel::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>

<body>
<?php
    // Ensure we have proper authentication context using SessionManager
    $userId = \App\Helpers\SessionManager::get('user_id');
    $userType = \App\Helpers\SessionManager::get('user_type');
    $isLoggedIn = \App\Helpers\SessionManager::isLoggedIn();
    
    // Debug authentication
    \Log::info('Quiz take authentication check', [
        'user_id' => $userId,
        'user_type' => $userType,
        'is_logged_in' => $isLoggedIn,
        'attempt_id' => $attempt->attempt_id,
        'attempt_student_id' => $attempt->student_id
    ]);
    
    if (!$isLoggedIn) {
        \Log::warning('Quiz access denied - not authenticated', [
            'user_id' => $userId,
            'is_logged_in' => $isLoggedIn
        ]);
        echo '<script>console.log("Authentication failed - redirecting to login"); window.location.href = "/";</script>';
        echo '<div style="text-align: center; padding: 50px;"><p>Redirecting to login...</p></div>';
        exit;
    }
    
    if ($userType !== 'student') {
        \Log::warning('Quiz access denied - not a student', [
            'user_id' => $userId,
            'user_type' => $userType
        ]);
        echo '<script>console.log("Access denied - not a student"); window.location.href = "/";</script>';
        echo '<div style="text-align: center; padding: 50px;"><p>Access denied. Student role required.</p></div>';
        exit;
    }
    
    // Check if this attempt is still active
    if ($attempt->status !== 'in_progress') {
        \Log::info('Quiz attempt not in progress', [
            'attempt_id' => $attempt->attempt_id,
            'status' => $attempt->status
        ]);
        
        // Redirect to results if already completed
        if ($attempt->status === 'completed') {
            echo '<script>console.log("Quiz already completed - redirecting to results"); window.location.href = "' . route('student.quiz.results', $attempt->attempt_id) . '";</script>';
            exit;
        } else {
            // Redirect to dashboard for other statuses
            echo '<script>console.log("Quiz attempt not available - status: ' . $attempt->status . '"); window.location.href = "' . route('student.dashboard') . '"; alert("Quiz attempt is not available.");</script>';
            exit;
        }
    }
    
    \Log::info('Quiz take access granted', [
        'user_id' => $userId,
        'attempt_id' => $attempt->attempt_id,
        'quiz_id' => $attempt->quiz_id
    ]);
?>

<div class="quiz-container">
    <!-- Quiz Header -->
    <div class="quiz-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1"><?php echo e($quiz->quiz_title); ?></h4>
                <p class="mb-0 opacity-75"><?php echo e($quiz->questions->count()); ?> questions</p>
            </div>
            <div class="text-end">
                <div class="student-info position-relative">
                    <strong><?php echo e($student->student_fname ?? $student->first_name ?? 'Student'); ?> <?php echo e($student->student_lname ?? $student->last_name ?? ''); ?></strong>
                    <br>
                    <small class="opacity-75"><?php echo e($student->student_id ?? $student->student_number ?? 'N/A'); ?></small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quiz Body -->
    <div class="quiz-body">
        <!-- Questions Panel -->
        <div class="questions-panel">
            <form id="quizForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="attempt_id" value="<?php echo e($attempt->attempt_id); ?>">
                
                <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="question-card" id="question-<?php echo e((int)$index + 1); ?>" style="<?php echo e((int)$index > 0 ? 'display: none;' : ''); ?>">
                        <div class="question-number"><?php echo e((int)$index + 1); ?></div>
                        
                        <div class="question-text">
                            <?php echo e($question->question_text); ?>

                        </div>
                        
                        <?php if($question->question_type === 'multiple_choice'): ?>
                            <?php
                                $options = is_array($question->options) ? $question->options : 
                                          (is_string($question->options) ? json_decode($question->options, true) : []);
                            ?>
                            
                            <?php if($options): ?>
                                <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionIndex => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="answer-option">
                                        <input type="radio" 
                                               name="answers[<?php echo e($question->id); ?>]" 
                                               value="<?php echo e(chr(65 + (int)$optionIndex)); ?>" 
                                               id="q<?php echo e($question->id); ?>_<?php echo e(chr(65 + (int)$optionIndex)); ?>"
                                               onchange="markAnswered(<?php echo e((int)$index + 1); ?>)">
                                        <label for="q<?php echo e($question->id); ?>_<?php echo e(chr(65 + (int)$optionIndex)); ?>">
                                            <strong><?php echo e(chr(65 + (int)$optionIndex)); ?>.</strong> <?php echo e($option); ?>

                                        </label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        <?php elseif($question->question_type === 'true_false'): ?>
                            <div class="answer-option">
                                <input type="radio"
                                       name="answers[<?php echo e($question->id); ?>]" 
                                       value="True"
                                       id="q<?php echo e($question->id); ?>_True"
                                       onchange="markAnswered(<?php echo e((int)$index + 1); ?>)">
                                <label for="q<?php echo e($question->id); ?>_True">
                                    <strong>True</strong>
                                </label>
                            </div>
                            <div class="answer-option">
                                <input type="radio"
                                       name="answers[<?php echo e($question->id); ?>]" 
                                       value="False"
                                       id="q<?php echo e($question->id); ?>_False"
                                       onchange="markAnswered(<?php echo e((int)$index + 1); ?>)">
                                <label for="q<?php echo e($question->id); ?>_False">
                                    <strong>False</strong>
                                </label>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </form>
        </div>
        
        <!-- Sidebar Panel -->
        <div class="sidebar-panel">
            <!-- Timer -->
            <?php if($timeRemaining !== null): ?>
                <div class="timer-display" id="timerDisplay">
                    <h6 class="mb-2">Time Remaining</h6>
                    <div class="timer-value" id="timerValue"><?php echo e($timeRemaining); ?>:00</div>
                </div>
            <?php endif; ?>
            
            <!-- Progress Overview -->
            <div class="progress-overview">
                <h6 class="mb-2">Progress</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Answered:</span>
                    <span id="answeredCount">0</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Remaining:</span>
                    <span id="remainingCount"><?php echo e($quiz->questions->count()); ?></span>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar" id="progressBar" style="width: 0%"></div>
                </div>
                
                <!-- Question Grid -->
                <div class="question-grid">
                    <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" 
                                class="question-btn <?php echo e((int)$index === 0 ? 'current' : ''); ?>" 
                                id="qBtn-<?php echo e((int)$index + 1); ?>"
                                onclick="goToQuestion(<?php echo e((int)$index + 1); ?>)">
                            <?php echo e((int)$index + 1); ?>

                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            
            <!-- Submit Section -->
            <div class="submit-section">
                <button type="button" class="btn btn-success btn-sm w-100 mb-3" onclick="reviewAnswers()">
                    <i class="bi bi-eye"></i> Review Answers
                </button>
                <button type="button" class="btn btn-primary w-100" onclick="submitQuiz()">
                    <i class="bi bi-check-circle"></i> Submit Quiz
                </button>
            </div>
        </div>
    </div>
    
    <!-- Quiz Actions -->
    <div class="quiz-actions">
        <div class="navigation-buttons">
            <button type="button" class="btn btn-outline-secondary" id="prevBtn" onclick="previousQuestion()" disabled>
                <i class="bi bi-chevron-left"></i> Previous
            </button>
            <button type="button" class="btn btn-outline-primary" id="nextBtn" onclick="nextQuestion()">
                Next <i class="bi bi-chevron-right"></i>
            </button>
        </div>
        
        <div class="quiz-info">
            <span class="text-muted fw-bold">Question <span id="currentQuestion">1</span> of <?php echo e($quiz->questions->count()); ?></span>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let currentQuestionIndex = 1;
    const totalQuestions = <?php echo e($quiz->questions->count()); ?>;
    const timeLimit = <?php echo e($timeRemaining ?? 0); ?>;
    let timeRemaining = timeLimit * 60; // Convert to seconds
    let timerInterval;
    
    // Initialize quiz
    document.addEventListener('DOMContentLoaded', function() {
        updateNavigationButtons();
        updateProgress();
        
        <?php if($timeRemaining !== null): ?>
            startTimer();
        <?php endif; ?>
        
        // Prevent page reload/close without confirmation
        window.addEventListener('beforeunload', function(e) {
            e.preventDefault();
            e.returnValue = 'Are you sure you want to leave? Your quiz progress will be lost.';
        });
    });
    
    function startTimer() {
        timerInterval = setInterval(function() {
            timeRemaining--;
            updateTimerDisplay();
            
            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                autoSubmitQuiz();
            }
        }, 1000);
    }
    
    function updateTimerDisplay() {
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        const display = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        const timerValue = document.getElementById('timerValue');
        const timerDisplay = document.getElementById('timerDisplay');
        
        if (timerValue) {
            timerValue.textContent = display;
        }
        
        if (timerDisplay) {
            // Update timer color based on remaining time
            timerDisplay.className = 'timer-display';
            if (timeRemaining <= 300) { // 5 minutes
                timerDisplay.classList.add('danger');
            } else if (timeRemaining <= 600) { // 10 minutes
                timerDisplay.classList.add('warning');
            }
        }
    }
    
    function goToQuestion(questionNumber) {
        // Hide current question
        document.querySelector(`#question-${currentQuestionIndex}`).style.display = 'none';
        document.querySelector(`#qBtn-${currentQuestionIndex}`).classList.remove('current');
        
        // Show target question
        currentQuestionIndex = questionNumber;
        document.querySelector(`#question-${currentQuestionIndex}`).style.display = 'block';
        document.querySelector(`#qBtn-${currentQuestionIndex}`).classList.add('current');
        
        // Update displays
        document.getElementById('currentQuestion').textContent = currentQuestionIndex;
        updateNavigationButtons();
    }
    
    function nextQuestion() {
        if (currentQuestionIndex < totalQuestions) {
            goToQuestion(currentQuestionIndex + 1);
        }
    }
    
    function previousQuestion() {
        if (currentQuestionIndex > 1) {
            goToQuestion(currentQuestionIndex - 1);
        }
    }
    
    function updateNavigationButtons() {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        
        prevBtn.disabled = currentQuestionIndex === 1;
        nextBtn.disabled = currentQuestionIndex === totalQuestions;
        
        if (currentQuestionIndex === totalQuestions) {
            nextBtn.innerHTML = 'Finish <i class="bi bi-check-circle"></i>';
            nextBtn.onclick = submitQuiz;
        } else {
            nextBtn.innerHTML = 'Next <i class="bi bi-chevron-right"></i>';
            nextBtn.onclick = nextQuestion;
        }
    }
    
    function markAnswered(questionNumber) {
        const questionBtn = document.querySelector(`#qBtn-${questionNumber}`);
        if (questionBtn) {
            questionBtn.classList.add('answered');
        }
        updateProgress();
    }
    
    function updateProgress() {
        const answeredQuestions = document.querySelectorAll('.question-btn.answered').length;
        const remainingQuestions = totalQuestions - answeredQuestions;
        
        document.getElementById('answeredCount').textContent = answeredQuestions;
        document.getElementById('remainingCount').textContent = remainingQuestions;
        
        const progressPercentage = (answeredQuestions / totalQuestions) * 100;
        document.getElementById('progressBar').style.width = progressPercentage + '%';
    }
    
    function reviewAnswers() {
        const modal = `
            <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reviewModalLabel">Review Your Answers</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                ${generateReviewContent()}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="submitQuiz()">Submit Quiz</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal
        const existingModal = document.getElementById('reviewModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        document.body.insertAdjacentHTML('beforeend', modal);
        const bootstrapModal = new bootstrap.Modal(document.getElementById('reviewModal'));
        bootstrapModal.show();
    }
    
    function generateReviewContent() {
        let content = '';
        for (let i = 1; i <= totalQuestions; i++) {
            const questionElement = document.querySelector(`#question-${i}`);
            const selectedAnswer = questionElement.querySelector('input[type="radio"]:checked');
            const answerText = selectedAnswer ? selectedAnswer.nextElementSibling.textContent.trim() : 'Not answered';
            
            content += `
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Question ${i}</h6>
                            <p class="card-text small">${questionElement.querySelector('.question-text').textContent.trim()}</p>
                            <div class="answer ${selectedAnswer ? 'text-success' : 'text-danger'}">
                                <strong>Answer:</strong> ${answerText}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        return content;
    }
    
    function submitQuiz() {
        // Convert letter answers (A, B, C) to index answers (0, 1, 2)
    function convertLetterAnswersToIndex(answers) {
        const convertedAnswers = {};
        
        for (const questionId in answers) {
            const answer = answers[questionId];
            
            // If answer is a letter (A, B, C, etc.)
            if (typeof answer === 'string' && answer.match(/^[A-Z]$/)) {
                // Convert to index (A=0, B=1, C=2, etc.)
                const index = answer.charCodeAt(0) - 65; // ASCII 'A' is 65
                convertedAnswers[questionId] = index.toString();
            } else {
                convertedAnswers[questionId] = answer;
            }
        }
        
        console.log('Original answers:', answers);
        console.log('Converted answers:', convertedAnswers);
        return convertedAnswers;
    }

        const unansweredQuestions = totalQuestions - document.querySelectorAll('.question-btn.answered').length;
        
        if (unansweredQuestions > 0) {
            if (!confirm(`You have ${unansweredQuestions} unanswered question(s). Are you sure you want to submit?`)) {
                return;
            }
        }
        
        // Disable submit button to prevent double submission
        const submitButtons = document.querySelectorAll('button[onclick="submitQuiz()"]');
        submitButtons.forEach(btn => {
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting...';
        });
        
        const formData = new FormData(document.getElementById('quizForm'));
        const answers = {};
        
        for (let [key, value] of formData.entries()) {
            if (key.startsWith('answers[')) {
                const questionId = key.match(/\[(\d+)\]/)[1];
                answers[questionId] = value;
            }
        }
        
        // Clear timer
        if (timerInterval) {
            clearInterval(timerInterval);
        }
        
        // Remove beforeunload listener
        window.removeEventListener('beforeunload', function() {});
        
        // Submit quiz with proper error handling
        fetch(`<?php echo e(route('student.quiz.submit.attempt', ['attemptId' => $attempt->attempt_id])); ?>`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ answers: convertLetterAnswersToIndex(answers) })
        })
        .then(response => {
            if (!response.ok) {
                // Handle HTTP error status
                return response.json().then(errorData => {
                    throw new Error(errorData.message || `HTTP ${response.status}: ${response.statusText}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                alert(`Quiz submitted successfully! Score: ${data.score}%`);
                window.location.href = data.redirect;
            } else {
                throw new Error(data.message || 'Unknown error occurred');
            }
        })
        .catch(error => {
            console.error('Submission Error:', error);
            
            // Re-enable submit buttons
            submitButtons.forEach(btn => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle"></i> Submit Quiz';
            });
            
            // Handle specific error cases
            if (error.message.includes('already completed')) {
                alert('This quiz has already been submitted. Redirecting to results...');
                window.location.href = `<?php echo e(route('student.quiz.results', ['attemptId' => $attempt->attempt_id])); ?>`;
            } else if (error.message.includes('Access denied')) {
                alert('Access denied. Please log in again.');
                window.location.href = '<?php echo e(route("login")); ?>';
            } else {
                alert('Error submitting quiz: ' + error.message + '\n\nPlease try again or contact support if the problem persists.');
            }
        });
    }
    
    function autoSubmitQuiz() {
        alert('Time is up! Your quiz will be submitted automatically.');
        submitQuiz();
    }
    
    // Add touch/swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    document.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    });
    
    document.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });
    
    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0 && currentQuestionIndex < totalQuestions) {
                // Swipe left - next question
                nextQuestion();
            } else if (diff < 0 && currentQuestionIndex > 1) {
                // Swipe right - previous question
                previousQuestion();
            }
        }
    }
    
    // Add keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowRight' && currentQuestionIndex < totalQuestions) {
            nextQuestion();
        } else if (e.key === 'ArrowLeft' && currentQuestionIndex > 1) {
            previousQuestion();
        } else if (e.key === 'Enter' && e.ctrlKey) {
            submitQuiz();
        }
    });
</script>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\student\quiz\take.blade.php ENDPATH**/ ?>