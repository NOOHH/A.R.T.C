@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-cloud-check"></i> Cloud Security Quiz Generator</h5>
                </div>
                <div class="card-body">
                    <p class="lead">Generate quizzes based on Cloud Security and GRC lecture materials.</p>
                    
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <!-- Default Quiz Generator -->
                    <div class="mb-4">
                        <h5>Generate Quiz from Default Lecture Materials</h5>
                        <p>Create a quiz using the following materials:</p>
                        <ul>
                            <li>Lecture 7 - Cloud Security Part 1</li>
                            <li>Lecture 7 - Cloud Security Part 2</li>
                            <li>Lecture 8 - GRC (Governance, Risk, and Compliance)</li>
                        </ul>
                        <form action="{{ route('cloud-security.generate') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="min_mcq">Minimum Multiple Choice Questions</label>
                                        <input type="number" name="min_mcq" id="min_mcq" class="form-control" value="10" min="5" max="20">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="min_tf">Minimum True/False Questions</label>
                                        <input type="number" name="min_tf" id="min_tf" class="form-control" value="8" min="3" max="15">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary" id="generateBtn">
                                    <i class="bi bi-lightning"></i> Generate Quiz
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <hr>
                    
                    <!-- Upload Custom PDFs -->
                    <div class="mt-4">
                        <h5>Generate Quiz from Custom Materials</h5>
                        <p>Upload your own PDF materials to create a custom quiz:</p>
                        <form action="{{ route('cloud-security.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="pdfs">Upload PDF Files (Max 10MB each)</label>
                                <input type="file" name="pdfs[]" id="pdfs" class="form-control" multiple accept=".pdf">
                                <small class="text-muted">You can select multiple PDF files</small>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="custom_min_mcq">Minimum Multiple Choice Questions</label>
                                        <input type="number" name="min_mcq" id="custom_min_mcq" class="form-control" value="10" min="5" max="20">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="custom_min_tf">Minimum True/False Questions</label>
                                        <input type="number" name="min_tf" id="custom_min_tf" class="form-control" value="8" min="3" max="15">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-success" id="uploadBtn">
                                    <i class="bi bi-upload"></i> Upload PDFs & Generate Quiz
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle default quiz generation button
    document.querySelector('form[action="{{ route("cloud-security.generate") }}"]').addEventListener('submit', function() {
        const btn = document.getElementById('generateBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating Quiz...';
    });
    
    // Handle custom PDF upload and generation button
    document.querySelector('form[action="{{ route("cloud-security.upload") }}"]').addEventListener('submit', function() {
        const btn = document.getElementById('uploadBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading & Generating...';
    });
});
</script>
@endpush
@endsection
