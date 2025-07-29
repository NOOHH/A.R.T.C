@extends('professor.professor-layouts.professor-layout')

@section('title', 'Settings')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Professor Settings</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('professor.settings.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="professor_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="professor_name" value="{{ $professor->professor_name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="professor_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="professor_email" value="{{ $professor->professor_email }}" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notification Preferences</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="email_notifications" id="email_notifications" value="1">
                            <label class="form-check-label" for="email_notifications">
                                Email Notifications
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="sms_notifications" id="sms_notifications" value="1">
                            <label class="form-check-label" for="sms_notifications">
                                SMS Notifications
                            </label>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="timezone" class="form-label">Timezone</label>
                            <select class="form-select" name="timezone" id="timezone">
                                <option value="Asia/Manila">Asia/Manila</option>
                                <option value="UTC">UTC</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="language" class="form-label">Language</label>
                            <select class="form-select" name="language" id="language">
                                <option value="en">English</option>
                                <option value="fil">Filipino</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
