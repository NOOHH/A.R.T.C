@extends('layouts.navbar')

@section('title', 'Debug Navbar Test')

@section('content')
<div class="container mt-5 pt-5">
    <h1>Debug Navbar Test</h1>
    
    <div class="card">
        <div class="card-body">
            <h3>Navbar Data Debug:</h3>
            <pre>{{ print_r($navbar ?? 'NO NAVBAR VARIABLE', true) }}</pre>
            
            <h3>Settings Data Debug:</h3>
            <pre>{{ print_r($settings ?? 'NO SETTINGS VARIABLE', true) }}</pre>
            
            <h3>All View Data:</h3>
            <pre>{{ print_r(get_defined_vars(), true) }}</pre>
        </div>
    </div>
</div>
@endsection
