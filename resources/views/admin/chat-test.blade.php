@extends('admin.admin-dashboard-layout')

@section('title', 'Admin Chat Test')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Admin Chat Test</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>Chat Test Instructions:</h5>
                        <ol>
                            <li>Look for the chat button (💬) in the top right corner of the page</li>
                            <li>Click the chat button to open the chat panel</li>
                            <li>Select a user type (Students, Professors, etc.)</li>
                            <li>Search for users and test the chat functionality</li>
                        </ol>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Debug Information:</h6>
                            <ul>
                                <li>Session User ID: {{ session('user_id') }}</li>
                                <li>Session User Name: {{ session('user_name') }}</li>
                                <li>Session User Role: {{ session('user_role') }}</li>
                                <li>Is Logged In: {{ session('logged_in') ? 'Yes' : 'No' }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>API Test:</h6>
                            <button class="btn btn-primary" onclick="testChatAPI()">Test Chat API</button>
                            <div id="apiTestResult" class="mt-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function testChatAPI() {
    const resultDiv = document.getElementById('apiTestResult');
    resultDiv.innerHTML = '<div class="text-info">Testing API...</div>';
    
    fetch('/api/chat/session/users?type=student&q=test')
        .then(response => response.json())
        .then(data => {
            resultDiv.innerHTML = `
                <div class="text-success">
                    <strong>API Test Result:</strong><br>
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                </div>
            `;
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <div class="text-danger">
                    <strong>API Test Failed:</strong><br>
                    ${error.message}
                </div>
            `;
        });
}
</script>
@endsection
