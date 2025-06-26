@extends('layouts.navbar') {{-- uses layouts/app.blade.php --}}

@section('title', 'Home') {{-- optional for dynamic title --}}

@section('content') {{-- this will be injected into @yield('content') in your layout --}}
<div style="display: flex; justify-content: center; align-items: center; height: 60vh;">
    <a href="{{ url('/enrollment') }}" style="font-size: 4rem; font-weight: bold; color: #fff; letter-spacing: 2px; text-decoration: none; transition: color 0.2s;">
        ENROLL NOW
    </a>
</div>
@endsection
