@extends('layouts.navbar') {{-- uses layouts/app.blade.php --}}

@section('title', 'Home') {{-- optional for dynamic title --}}

@push('styles')
<style>
{!! App\Helpers\SettingsHelper::getHomepageStyles() !!}
</style>
@endpush

@section('content') {{-- this will be injected into @yield('content') in your layout --}}
@php
    $settings = App\Helpers\SettingsHelper::getSettings();
    $homepageTitle = $settings['homepage']['title'] ?? 'ENROLL NOW';
@endphp

<div style="display: flex; justify-content: center; align-items: center; height: 60vh;">
    <a href="{{ url('/enrollment') }}" class="enroll-link" style="font-size: 4rem; font-weight: bold; letter-spacing: 2px; text-decoration: none; transition: all 0.3s ease; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
        {{ $homepageTitle }}
    </a>
</div>
@endsection
