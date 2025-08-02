# PowerShell script to test admin quiz creation
$testData = @{
    title = "PowerShell Test Quiz - $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
    description = "Testing the quiz_title fix via PowerShell"
    program_id = "41"
    module_id = "79" 
    course_id = "52"
    admin_id = 1
    quiz_id = $null
    time_limit = 60
    max_attempts = 1
    infinite_retakes = $false
    has_deadline = $false
    due_date = $null
    is_draft = $true
    status = "draft"
    questions = @(
        @{
            question_text = "Is the quiz_title error fixed?"
            question_type = "multiple_choice"
            points = 1
            explanation = "Testing the fix implementation"
            options = @("Yes", "No", "Maybe", "Unknown")
            correct_answers = @(0)
            order = 1
        }
    )
}

$jsonBody = $testData | ConvertTo-Json -Depth 5
$headers = @{
    'Content-Type' = 'application/json'
    'Accept' = 'application/json'
}

Write-Host "=== PowerShell Admin Quiz Test ===" -ForegroundColor Green
Write-Host "Testing URL: http://localhost:8000/admin/quiz-generator/save-quiz"
Write-Host "Request size: $($jsonBody.Length) bytes"
Write-Host ""

try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000/admin/quiz-generator/save-quiz" -Method POST -Headers $headers -Body $jsonBody -TimeoutSec 30
    
    Write-Host "✅ Response Status: $($response.StatusCode)" -ForegroundColor Green
    Write-Host "Response Content:" -ForegroundColor Yellow
    Write-Host $response.Content
    
    if ($response.StatusCode -eq 200) {
        Write-Host "🎉 SUCCESS: Admin quiz creation working!" -ForegroundColor Green
    }
    
} catch {
    $errorResponse = $_.Exception.Response
    if ($errorResponse) {
        $statusCode = [int]$errorResponse.StatusCode
        Write-Host "❌ Error Status: $statusCode" -ForegroundColor Red
        
        if ($statusCode -eq 500) {
            Write-Host "Server Error - Check Laravel logs for details" -ForegroundColor Red
        } elseif ($statusCode -eq 422) {
            Write-Host "Validation Error - Check request data" -ForegroundColor Yellow
        } elseif ($statusCode -eq 401 -or $statusCode -eq 403) {
            Write-Host "Authentication Error - Admin login required" -ForegroundColor Yellow
        }
    }
    
    Write-Host "Error details: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "=== Check Laravel Logs ===" -ForegroundColor Cyan
Write-Host "Run: Get-Content storage\logs\laravel.log -Tail 10"
